<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Event;
use AppBundle\Entity\History;
use AppBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ViewRegistrationController extends Controller
{
    /**
     * @Route("/registration/view/{registrationId}", name="viewRegistration")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return Response
     */
    public function viewRegistrationPage($registrationId)
    {
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        $currentEvent = $this->getDoctrine()->getRepository(Event::class)->getCurrentEvent();

        if (!$registration instanceof Registration) {
            return $this->redirectToRoute('listRegistrations');
        }

        $event = $registration->getEvent();
        $registrationType = $registration->getRegistrationType();
        $registrationStatus = $registration->getRegistrationStatus();
        $registrationHistory = $this->getDoctrine()->getRepository(History::class)->getHistoryFromRegistration($registration);
        $badges = $registration->getBadges();

        $info = '';
        if (!$registrationStatus->getActive()) {
            $info = $registrationStatus->getDescription();
            if ($registrationStatus->getStatus() == 'Transferred') {
                $transferredRegistration = $registration->getTransferredTo();
                if ($transferredRegistration) {
                    $url = $this->generateUrl('viewRegistration',
                        ['registrationId' => $transferredRegistration->getRegistrationId()]);
                    $info .= " Transferred to <a href='$url'>" . $transferredRegistration->getFirstName()
                        . ' ' . $transferredRegistration->getLastName() . '</a>. ';
                }
            }
        }

        $lastBadgeID = 'fixme';

        $vars = [
            'registration' => $registration,
            'event' => $event,
            'currentEvent' => $currentEvent,
            'registrationType' => $registrationType,
            'registrationStatus' => $registrationStatus,
            'history' => $registrationHistory,
            'badges' => $badges,
            'registrationShirts' => $registration->getRegistrationShirts(),
            'extras' => $registration->getExtras(),
            'info' => $info,
            'lastBadgeId' => $lastBadgeID,
        ];

        return $this->render('registration/viewRegistration.html.twig', $vars);
    }

}
