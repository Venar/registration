<?php

namespace AppBundle\Controller\Utils;

use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ErrorLocatorController extends Controller
{
    /**
     * @Route("/error/finder", name="error_finder")
     * @Security("has_role('ROLE_USER')")
     */
    public function get_errorFinder() {
        $vars = [];
        $currentYear = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder->select('r2.firstName', 'r2.lastName', 'YEAR(r2.birthday) as year', 'count(r2.id) as NameCount')
            ->from(Registration::class, 'r2')
            ->innerJoin(Registration::class, 'r', Join::WITH, 'r.id = r2.id')
            ->where('r2.event = :event')
            ->groupBy('r2.firstName', 'r2.lastName', 'year')
            ->having('NameCount > 1')
            ->setParameter('event', $currentYear)
            ;

        $vars['duplicatesYear'] = $queryBuilder->getQuery()->getResult();
        foreach ($vars['duplicatesYear'] as &$duplicate) {
            $registrations = $this->getDoctrine()->getRepository(Registration::class)->getFromFirstLastBirthyear(
                $duplicate['firstName'],
                $duplicate['lastName'],
                $duplicate['year'],
                $currentYear
            );
            $duplicate['registrations'] = $registrations;
        }

        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder->select('r2.firstName', 'r2.lastName', 'r2.email', 'count(r2.id) as NameCount')
            ->from(Registration::class, 'r2')
            ->innerJoin(Registration::class, 'r', Join::WITH, 'r.id = r2.id')
            ->where('r2.event = :event')
            ->groupBy('r2.firstName', 'r2.lastName', 'r2.email')
            ->having('NameCount > 1')
            ->setParameter('event', $currentYear)
        ;
        $vars['duplicatesEmail'] = $queryBuilder->getQuery()->getResult();
        foreach ($vars['duplicatesEmail'] as &$duplicate) {
            $registrations = $this->getDoctrine()->getRepository(Registration::class)->getFromFirstLastEmail(
                $duplicate['firstName'],
                $duplicate['lastName'],
                $duplicate['email'],
                $currentYear
            );
            $duplicate['registrations'] = $registrations;
        }

        $vars['tooYoung'] = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->getRegistrationsLessThanAYearOld($currentYear);

        return $this->render('utils/errorLocator.html.twig', $vars);
    }
}
