<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Printing;

use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query\Expr\Join;

class BadgeCSVListController extends Controller
{
    /**
     * @Route("/print/csv/{type}", name="print_csv")
     * @Security("has_role('ROLE_USER')")
     *
     * @param string $type
     */
    public function printingList($type)
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();

        $order = [];

        $badgeTypeRepository = $this->getDoctrine()->getRepository(BadgeType::class);

        $badgeTypes = [];
        $show_group = false;
        switch ($type) {
            case 'staff':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('STAFF');
                break;
            case 'sponsor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGSPONSOR');
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGCOMMSPONSOR');
                break;
            case 'standard':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGSTANDARD');
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('MINOR');
                break;
            case 'group':
                $show_group = true;
                $order[] = ['regGroupName', 'ASC'];
                break;
            case 'guest':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('GUEST');
                break;
            case 'exhibitor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('EXHIBITOR');
                break;
            case 'vendor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('VENDOR');
                break;
        }

        $badgesSubQuery = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
            ->select('IDENTITY(b2.registration)')
            ->from('AppBundle:Badge', 'b2');

        for ($i = 0; $i < count($badgeTypes); $i++) {
            if ($i == 0) {
                $badgesSubQuery
                    ->where("b2.badgetype = :type$i");
            } else {
                $badgesSubQuery
                    ->orWhere("b2.badgetype = :type$i");
            }
            $queryBuilder->setParameter("type$i", $badgeTypes[$i]);
        }
        $badgesSubQueryDQL = $badgesSubQuery->getDQL();

        $registrationStatusSubQueryDQL = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
            ->select('rs.registrationstatusId')
            ->from('AppBundle\Entity\RegistrationStatus', 'rs')
            ->where('rs.active = :active')
            ->getDQL();

        $queryBuilder
            ->select([
                'r.number',
                'r.badgename',
                'r.firstname',
                'r.lastname',
                'b.number as badgeNumber',
                'bt.name as type',
                'rg.name as regGroupName',
                'r.confirmationnumber',
                'ex.name as extra'
            ])
            ->from('AppBundle\Entity\Registration', 'r')
            ->innerJoin('AppBundle\Entity\Badge', 'b', Join::WITH, 'b.registration = r.registrationId')
            ->innerJoin('AppBundle\Entity\BadgeStatus', 'bs', Join::WITH, 'bs.badgestatusId = b.badgestatus')
            ->innerJoin('AppBundle\Entity\BadgeType', 'bt', Join::WITH, 'bt.badgetypeId = b.badgetype')
            ->leftJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH,
                'rrg.registration = r.registrationId')
            ->leftJoin('AppBundle\Entity\Group', 'rg', Join::WITH, 'rg.reggroupId = rrg.reggroup')
            ->leftJoin('AppBundle\Entity\Registrationextra', 'rx', Join::WITH, 'rx.registration = r.registrationId')
            ->leftJoin('AppBundle\Entity\Extra', 'ex', Join::WITH, 'rx.extra = ex.extraId')
            ->where($queryBuilder->expr()->in('r.registrationstatus', $registrationStatusSubQueryDQL))
            ->andWhere($queryBuilder->expr()->in('r.registrationId', $badgesSubQueryDQL))
            ->andWhere('r.event = :event')
            ->andWhere('bs.active = :bsactive')
            ->setParameter('event', $event)
            ->setParameter('active', true)
            ->setParameter('bsactive', true);


        if ($type != 'staff') {
            $staffBadge = $badgeTypeRepository->getBadgeTypeFromType('STAFF');

            $allStaffBadges = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
                ->select('IDENTITY(b3.registration)')
                ->from('AppBundle:Badge', 'b3')
                ->where("b3.badgetype = :stafftype")
                ->innerJoin('b3.registration', 'b3r')
                ->andWhere('b3r.event = :b3revent')
                ->getDQL();

            $queryBuilder->andWhere($queryBuilder->expr()->notIn('r.registrationId', $allStaffBadges));
            $queryBuilder->setParameter('stafftype', $staffBadge)
                ->setParameter('b3revent', $event);
        }

        if ($show_group) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('rg.reggroupId'));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('rg.reggroupId'));
        }

        $order[] = ['r.number', 'ASC'];
        $order[] = ['bt.badgetypeId', 'DESC'];
        for ($i = 0; $i < count($order); $i++) {
            if ($i == 0) {
                $queryBuilder->orderBy($order[$i][0], $order[$i][1]);
            } else {
                $queryBuilder->addOrderBy($order[$i][0], $order[$i][1]);
            }
        }

        $registrations = $queryBuilder
            ->groupBy('r.registrationId')
            ->getQuery()
            ->getArrayResult();

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"badgeList.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');

        $fields = [
            'Badge',
            'Confirmation Number',
            'LastName',
            'FirstName',
            'BadgeName',
            'badgetype',
            'Group',
            'Extra',
            'Signature',
        ];

        fputcsv($handle, $fields);

        foreach ($registrations as $registration) {
            $regNumber = $registration['number'];
            $regName = $registration['badgename'];
            $firstName = $registration['firstname'];
            $lastName = $registration['lastname'];
            $badgeType = $registration['type'];
            $groupName = $registration['regGroupName'];
            $extra = $registration['extra'];
            $confirmationNumber = $registration['confirmationnumber'];

            $data = [
                $regNumber,
                $confirmationNumber,
                $firstName,
                $lastName,
                $regName,
                $badgeType,
                $groupName,
                $extra,
                'X_______________________',
            ];

            fputcsv($handle, $data);
        }

        fclose($handle);
        exit;
    }
}
