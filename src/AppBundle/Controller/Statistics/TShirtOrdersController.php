<?php

namespace AppBundle\Controller\Statistics;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TShirtOrdersController extends Controller
{
    /**
     * @Route("/shirts/list", name="shirt_list")
     * @Security("has_role('ROLE_USER')")
     */
    public function tShirtOrders()
    {
        $vars = [];

        $event = $this->get('repository_event')->getSelectedEvent();
        $shirts = $this->get('repository_shirt')->findAll();

        $subQuery = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
            ->select('r')
            ->from('AppBundle:Registration', 'r')
            ->where("r.event = :event")
            ->getDQL();

        $totalTShirts = 0;
        $data = [];
        foreach ($shirts as $shirt) {
            $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
            $queryBuilder
                ->select('count(rs.registrationshirtId)')
                ->from('AppBundle\Entity\Registrationshirt', 'rs')
                ->where($queryBuilder->expr()->in('rs.registration', $subQuery))
                ->andWhere('rs.shirt = :shirt')
                ->setParameter('shirt', $shirt)
                ->setParameter('event', $event)
            ;
            $count = (int) $queryBuilder->getQuery()->getSingleScalarResult();

            $totalTShirts += $count;
            $shirtType = $shirt->getShirttype() . ' ' . $shirt->getShirtsize();

            $tmpArray = [];
            $tmpArray[] = $shirtType;
            $tmpArray[] = $count;
            $data[] = $tmpArray;
        }

        $vars['totalCount'] = $totalTShirts;
        $vars['event'] = $event;
        $vars['data'] = $data;
        $vars['dataJson'] = json_encode($data);

        return $this->render('statistics/tShirtOrders.html.twig', $vars);
    }

    /**
     * @Route("/shirts/csv", name="shirt_csv")
     * @Security("has_role('ROLE_USER')")
     */
    public function shirtCSV() {
        $registrations = $this->get('repository_registration')->findRegistrationsWithShirts();

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"ad_shirts.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');

        $fields = [
            'Badge',
            'LastName',
            'FirstName',
            'BadgeName',
            'badgetype',
            'type',
            'size',
            'Group',
            '# of Shirts',
            'Signature',
        ];

        fputcsv($handle, $fields);

        foreach ($registrations as $registration) {
            $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
            $badgeType = '';
            foreach ($badges as $badge) {
                $badgeType = $badge->getBadgetype()->getDescription();
            }

            $group = $this->get('repository_reggroup')->getRegGroupFromRegistration($registration);
            $groupName = '';
            if ($group) {
                $groupName = $group->getName();
            }

            $shirts = $this->get('repository_shirt')->getShirtsFromRegistration($registration);
            foreach ($shirts as $shirt) {
                $data = [
                    $registration->getNumber(),
                    $registration->getLastname(),
                    $registration->getFirstname(),
                    $registration->getBadgename(),
                    $badgeType,
                    $shirt->getShirttype(),
                    $shirt->getShirtsize(),
                    $groupName,
                    count($shirts),
                    'X_______________________',
                ];

                fputcsv($handle, $data);
            }
        }

        fclose($handle);
        exit;
    }
}
