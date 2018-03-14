<?php

namespace AppBundle\Service\Util;


use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;

class Percent
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        $event = $this->entityManager->getRepository(Event::class)->getCurrentEvent();
        $remaining = $event->getAttendancecap();
        $badgeTypeStaff = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->getBadgeTypeFromType('STAFF');

        $allStaffBadges = $this->entityManager->createQueryBuilder()
            ->select('b.badgeId')
            ->from('AppBundle:Badge', 'b')
            ->where("b.badgetype = :stafftype")
            ->getDQL();

        $counts = [];
        $badgeTypes = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->findAll();
        foreach ($badgeTypes as $badgeType) {
            if ($badgeType->getBadgetypeId() == $badgeTypeStaff->getBadgetypeId()) {
                continue;
            }

            $allBadgesSubQuery = $this->entityManager->createQueryBuilder()
                ->select('b2.badgeId')
                ->from('AppBundle:Badge', 'b2')
                ->where("b2.badgetype = :type")
                ->getDQL();

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('count(r.registrationId)')
                ->from('AppBundle:Registration', 'r')
                ->innerJoin('r.registrationstatus', 'rs')
                ->where($queryBuilder->expr()->notIn('r.registrationId', $allStaffBadges))
                ->andWhere($queryBuilder->expr()->in('r.registrationId', $allBadgesSubQuery))
                ->andWhere('r.event = :event')
                ->andWhere('rs.active = :active')
                ->setParameter('stafftype', $badgeTypeStaff->getBadgetypeId())
                ->setParameter('type', $badgeType->getBadgetypeId())
                ->setParameter('event', $event->getId())
                ->setParameter('active', true)
            ;

            try {
                $count = $queryBuilder->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                $count = 0;
            }

            $counts[$badgeType->getName()] = $count;
        }

        $tmpBadge = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->getBadgeTypeFromType('ADREGSTANDARD');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->getBadgeTypeFromType('MINOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->getBadgeTypeFromType('ADREGSPONSOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this
            ->entityManager
            ->getRepository(BadgeType::class)
            ->getBadgeTypeFromType('ADREGCOMMSPONSOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $truePercent = 100 - (($remaining / $event->getAttendancecap()) * 100);

        return min([$truePercent, 100]);
    }
}