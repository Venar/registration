<?php

namespace AppBundle\Service\Util;


use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
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
            ->select('b.id')
            ->from(Badge::class, 'b')
            ->where("b.badgeType = :staffType")
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
                ->select('b2.id')
                ->from(Badge::class, 'b2')
                ->where("b2.badgeType = :type")
                ->getDQL();

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('count(r.id)')
                ->from(Registration::class, 'r')
                ->innerJoin('r.registrationStatus', 'rs')
                ->where($queryBuilder->expr()->notIn('r.id', $allStaffBadges))
                ->andWhere($queryBuilder->expr()->in('r.id', $allBadgesSubQuery))
                ->andWhere('r.event = :event')
                ->andWhere('rs.active = :active')
                ->setParameter('staffType', $badgeTypeStaff->getBadgeTypeId())
                ->setParameter('type', $badgeType->getBadgeTypeId())
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