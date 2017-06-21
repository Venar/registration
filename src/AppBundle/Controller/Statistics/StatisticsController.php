<?php

namespace AppBundle\Controller\Statistics;

ini_set('max_execution_time', 300);

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    /**
     * @Route("/stats")
     */
    public function numberAction()
    {
        $vars = [];
        $event = $this->get('repository_event')->getSelectedEvent();
        $vars['year'] = $event->getStartdate()->format('Y');

        $eventYearEnd = $event->getStartdate()->format('Y');
        $eventYearOpen = $event->getStartdate()->format('Y') - 1;

        $remaining  = $event->getAttendancecap();

        $startIntervalSeconds = strtotime("June 1st $eventYearOpen");
        $startInterval = new \DateTime("@$startIntervalSeconds");
        $endIntervalSeconds = strtotime("May 1st $eventYearEnd");
        $endInterval = new \DateTime("@$endIntervalSeconds");

        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($startInterval, $interval, $endInterval);
        $months = [];
        foreach ($period as $dateTime) {
            /* @var $dateTime \DateTime */
            $months[] = $dateTime->format("F Y");
        }

        $counts = array();
        $dataByType = [];
        $staffBadge = $this->get('repository_badgetype')->getBadgeTypeFromType('STAFF');

        $allStaffBadges = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
            ->select('b.badgeId')
            ->from('AppBundle:Badge', 'b')
            ->where("b.badgetype = :stafftype")
            ->getDQL();

        $badgeTypes = $this->get('repository_badgetype')->findAll();
        foreach ($badgeTypes as $badgeType) {
            $tmp = [];
            $tmpData = [];

            $total_count = 0;
            foreach ($months as $month) {
                $start   = date('Y-m-01 H:i:s.u',strtotime($month));
                $endDateTime = new \Datetime($month);
                $endDateTime->modify('first day of next month');
                $end = $endDateTime->format('Y-m-01 H:i:s.u');

                $allBadgesSubQuery = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
                    ->select('IDENTITY(b2.registration)')
                    ->from('AppBundle:Badge', 'b2')
                    ->where("b2.badgetype = :type")
                    ->getDQL();

                $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
                $queryBuilder
                    ->select('count(r.registrationId)')
                    ->from('AppBundle:Registration', 'r')
                    ->where($queryBuilder->expr()->in('r.registrationId', $allBadgesSubQuery))
                    ->andWhere('r.event = :event')
                    ->andWhere('r.createddate > :start')
                    ->andWhere('r.createddate <= :end')
                    ->setParameter('type', $badgeType->getBadgetypeId())
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->setParameter('event', $event)
                ;
                if ($badgeType->getName() != 'STAFF') {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->notIn('r.registrationId', $allStaffBadges))
                        ->setParameter('stafftype', $staffBadge->getBadgetypeId());
                }

                $count = (int) $queryBuilder->getQuery()->getSingleScalarResult();
                $total_count += $count;
                $tmpData[] = $count;
            }

            $counts[$badgeType->getName()] = $total_count;

            if (array_sum($tmpData) == 0) {
                continue;
            }

            $tmp['name'] = $badgeType->getDescription();
            $tmp['data'] = $tmpData;
            $dataByType[] = $tmp;
        }
        $dataByTypeMonths = $months;

        $events = $this->get('repository_event')->findAll();
        $eventNames = [];
        foreach ($events as $event) {
            $eventNames[] = $event->getYear();
        }
        $monthsWithoutYear = [
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
            'January',
            'February',
            'March',
            'April',
        ];
        $dataByYear = $this->getDataByEventByDay($events);

        $staffCount   = (int)$counts[$staffBadge->getName()];
        //$remaining    = $remaining + $staffCount;
        $vars['staff_percent'] = $staffCount;

        $tmpBadge   = $this->get('repository_badgetype')->getBadgeTypeFromType('ADREGSTANDARD');
        $tmpCount   = (int)$counts[$tmpBadge->getName()];
        $remaining  = $remaining - $tmpCount;
        $vars['standard_percent'] = $tmpCount;

        $tmpBadge   = $this->get('repository_badgetype')->getBadgeTypeFromType('MINOR');
        $tmpCount   = (int)$counts[$tmpBadge->getName()];
        $remaining  = $remaining - $tmpCount;
        $vars['minor_percent'] = $tmpCount;

        $tmpBadge   = $this->get('repository_badgetype')->getBadgeTypeFromType('ADREGSPONSOR');
        $tmpCount   = (int)$counts[$tmpBadge->getName()];
        $remaining  = $remaining - $tmpCount;
        $vars['sponsor_percent'] = $tmpCount;

        $tmpBadge   = $this->get('repository_badgetype')->getBadgeTypeFromType('ADREGCOMMSPONSOR');
        $tmpCount = 0;
        if (array_key_exists($tmpBadge->getName(), $counts)) {
            $tmpCount = (int)$counts[$tmpBadge->getName()];
        }
        $remaining  = $remaining - $tmpCount;
        $vars['community_percent'] = $tmpCount;

        $vars['avail_percent'] = $remaining;

        $vars['data_by_type'] = json_encode($dataByType);
        $vars['categories'] = json_encode($dataByTypeMonths);

        $vars['data_by_year'] = json_encode($dataByYear);
        $vars['months'] = json_encode($monthsWithoutYear);

        return $this->render('statistics/statistics.html.twig', $vars);
    }


    /**
     * @param Event[] $events
     * @return mixed[]
     */
    public function getDataByEventByDay($events) {
        $data = [];
        $currentEvent = $this->get('repository_event')->getCurrentEvent();

        foreach ($events as $event) {
            $cache = new FilesystemAdapter();
            $statsField = "stats.cache.eventsByDay.{$event->getEventId()}";

            $cachedData = $cache->getItem($statsField);
            if ($cachedData->isHit())
            {
                $data = $cachedData->get();
                $tmp['name'] = $event->getYear();
                $tmp['data'] = unserialize($data);
                $data[]      = $tmp;

                continue;
            }

            $tmp             = [];
            $tmpData         = [];

            $lastCount = 0;
            $eventEndInSeconds = $event->getEnddate()->getTimestamp();
            $year = ((int) $event->getStartdate()->format('Y')) - 1;

            $statsStart = strtotime("May 1st $year");
            $secondsInADay = 86400;

            $totalForYear = self::getStatsByRange($event, $statsStart, $eventEndInSeconds);

            for ($day = $statsStart; $day < $eventEndInSeconds; $day += $secondsInADay) {
                $statsYear = 2016;
                if ((int)date('n', $day) < 5) {
                    $statsYear++;
                }
                $count = $this->getStatsByRange($event, $statsStart, $day);
                if ($lastCount != $count && $count != $totalForYear) {
                    $dayFormat = date("$statsYear-m-d H:i:s", $day);
                    $tmpData[] = [strtotime($dayFormat) * 1000, $count];
                }
                $lastCount = $count;
            }

            if (array_sum(array_column($tmpData, 1)) == 0) {
                continue;
            }

            if ($currentEvent->getEventId() != $event->getEventId()) {
                $cachedData->set(serialize($tmpData));
            }

            $tmp['name'] = $event->getYear();
            $tmp['data'] = $tmpData;
            $data[]      = $tmp;
        }

        return $data;
    }

    /**
     * @param Event $event
     * @param int $startSecs
     * @param int $endSecs
     * @return int
     */
    public function getStatsByRange($event, $startSecs, $endSecs) {
        $start = date('Y-m-d H:i:s', $startSecs);
        $end = date('Y-m-d H:i:s', $endSecs);

        $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
        $queryBuilder
            ->select('count(r.registrationId)')
            ->from('AppBundle:Registration', 'r')
            ->where('r.event = :event')
            ->andWhere('r.createddate > :start')
            ->andWhere('r.createddate <= :end')
            ->setParameter('event', $event)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ;
        $sql   = 'SELECT COUNT(`Registration`.Registration_ID) as count'
            .' FROM `Registration`'
            .' WHERE'
            .' `Registration`.Event_ID = '.$event->getEventId()
            .' AND `Registration`.CreatedDate > \''.$start.'\''
            .' AND `Registration`.CreatedDate < \''.$end.'\''
        ;

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
