<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Statistics;

use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use AppBundle\Entity\RegistrationShirt;
use AppBundle\Entity\Shirt;
use Doctrine\ORM\NonUniqueResultException;
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

        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $shirts = $this->getDoctrine()->getRepository(Shirt::class)->findAll();

        $subQuery = $this
            ->getDoctrine()
            ->getRepository(Registration::class)
            ->createQueryBuilder('r')
            ->where("r.event = :event")
            ->getDQL();

        $totalTShirts = 0;
        $data = [];
        foreach ($shirts as $shirt) {
            $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
            $queryBuilder
                ->select('count(rs.registrationShirtId)')
                ->from(RegistrationShirt::class, 'rs')
                ->where($queryBuilder->expr()->in('rs.registration', $subQuery))
                ->andWhere('rs.shirt = :shirt')
                ->setParameter('shirt', $shirt)
                ->setParameter('event', $event)
            ;

            try {
                $count = (int)$queryBuilder->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                $count = 0;
            }

            $totalTShirts += $count;
            $shirtType = $shirt->getType() . ' ' . $shirt->getSize();

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
        $registrations = $this
            ->getDoctrine()
            ->getRepository(Registration::class)
            ->findRegistrationsWithShirts();

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
            $badges = $registration->getBadges();
            $badgeType = '';
            foreach ($badges as $badge) {
                $badgeType = $badge->getBadgetype()->getDescription();
            }

            $groups = $registration->getGroups();
            $groupName = '';
            foreach ($groups as $group) {
                if ($groupName != '') {
                    $groupName .= ', ';
                }
                $groupName .= $group->getName();
            }

            $registrationShirts = $registration->getRegistrationShirts();
            foreach ($registrationShirts as $registrationShirt) {
                $shirt = $registrationShirt->getShirt();
                $data = [
                    $registration->getNumber(),
                    $registration->getLastName(),
                    $registration->getFirstName(),
                    $registration->getBadgeName(),
                    $badgeType,
                    $shirt->getType(),
                    $shirt->getSize(),
                    $groupName,
                    count($registrationShirts),
                    'X_______________________',
                ];

                fputcsv($handle, $data);
            }
        }

        fclose($handle);
        exit;
    }
}
