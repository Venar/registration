<?php

namespace AppBundle\Controller\Utils;

use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ErrorLocatorController extends Controller
{
    /**
     * @Route("/error/finder")
     * @Security("has_role('ROLE_USER')")
     */
    public function get_errorFinder() {
        $vars = [];
        $currentYear = $this->get('repository_event')->getSelectedEvent();

        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder->select('r2.firstname', 'r2.lastname', 'YEAR(r2.birthday) as year', 'count(r2.registrationId) as NameCount')
            ->from('AppBundle\Entity\Registration', 'r2')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = r2.registrationId')
            ->where('r2.event = :event')
            ->groupBy('r2.firstname', 'r2.lastname', 'year')
            ->having('NameCount > 1')
            ->setParameter('event', $currentYear)
            ;

        $vars['duplicatesYear'] = $queryBuilder->getQuery()->getResult();
        foreach ($vars['duplicatesYear'] as &$duplicate) {
            $registrations = $this->get('repository_registration')->getFromFirstLastBirthyear(
                $duplicate['firstname'],
                $duplicate['lastname'],
                $duplicate['year'],
                $currentYear
            );
            $duplicate['registrations'] = $registrations;
        }

        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder->select('r2.firstname', 'r2.lastname', 'r2.email', 'count(r2.registrationId) as NameCount')
            ->from('AppBundle\Entity\Registration', 'r2')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = r2.registrationId')
            ->where('r2.event = :event')
            ->groupBy('r2.firstname', 'r2.lastname', 'r2.email')
            ->having('NameCount > 1')
            ->setParameter('event', $currentYear)
        ;
        $vars['duplicatesEmail'] = $queryBuilder->getQuery()->getResult();
        foreach ($vars['duplicatesEmail'] as &$duplicate) {
            $registrations = $this->get('repository_registration')->getFromFirstLastEmail(
                $duplicate['firstname'],
                $duplicate['lastname'],
                $duplicate['email'],
                $currentYear
            );
            $duplicate['registrations'] = $registrations;
        }

        $vars['tooYoung'] = $this->get('repository_registration')->getRegistrationsLessThanAYearOld($currentYear);

        return $this->render('utils/errorLocator.html.twig', $vars);
    }
}
