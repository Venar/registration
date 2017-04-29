<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 2:46 PM
 */

namespace AppBundle\Service\Util;


use AppBundle\Service\Repository\BadgeTypeRepository;
use AppBundle\Service\Repository\EventRepository;
use Doctrine\ORM\EntityManager;

class Percent
{
    /** @var EventRepository $event */
    protected $event;
    /** @var BadgeTypeRepository $badgeType */
    protected $badgeType;
    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(EventRepository $event, BadgeTypeRepository $badgeType, EntityManager $entityManager)
    {
        $this->event = $event;
        $this->badgeType = $badgeType;
        $this->entityManager = $entityManager;
    }

    /**
     * @return float
     */
    public function getPercent() : float
    {
        $event = $this->event->getCurrentEvent();
        $remaining = $event->getAttendancecap();
        $badgeTypeStaff = $this->badgeType->getBadgeTypeFromType('STAFF');

        $allStaffBadges = $this->entityManager->createQueryBuilder()
            ->select('b.badgeId')
            ->from('AppBundle:Badge', 'b')
            ->where("b.badgetype = :stafftype")
            ->getDQL();

        $counts = [];
        $badgeTypes = $this->badgeType->findAll();
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
                ->where($queryBuilder->expr()->notIn('r.registrationId', $allStaffBadges))
                ->andWhere($queryBuilder->expr()->in('r.registrationId', $allBadgesSubQuery))
                ->andWhere('r.event = :event')
                ->setParameter('stafftype', $badgeTypeStaff->getBadgetypeId())
                ->setParameter('type', $badgeType->getBadgetypeId())
                ->setParameter('event', $event->getEventId())
            ;

            $count = $queryBuilder->getQuery()->getSingleScalarResult();

            $counts[$badgeType->getName()] = $count;
        }

        $tmpBadge = $this->badgeType->getBadgeTypeFromType('ADREGSTANDARD');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this->badgeType->getBadgeTypeFromType('MINOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this->badgeType->getBadgeTypeFromType('ADREGSPONSOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $tmpBadge = $this->badgeType->getBadgeTypeFromType('ADREGCOMMSPONSOR');
        $tmpCount = $counts[$tmpBadge->getName()];
        $remaining = $remaining - $tmpCount;

        $truePercent = 100 - (($remaining / $event->getAttendancecap()) * 100);

        return min([$truePercent, 100]);
    }
}