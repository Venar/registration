<?php

namespace AppBundle\Controller\Registration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ViewRegistrationController extends Controller
{
    /**
     * @Route("/registration/view/{registrationId}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return Response
     */
    public function viewRegistrationPage($registrationId)
    {
        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationId);

        $event = $registration->getEvent();
        $registrationType = $registration->getRegistrationtype();
        $registrationStatus = $registration->getRegistrationstatus();
        $registrationHistory = $this->get('repository_registrationhistory')->getHistoryFromRegistration($registration);
        $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);

        $info = '';
        if ($registrationStatus->getActive()) {
            $info = $registrationStatus->getDescription();
            if ($registrationStatus->getStatus() == 'Transfered') {
                $transferredRegistration = $registration->getTransferedto();
                $info .= " Transferred to <a href='/registration/view/" . $transferredRegistration->getRegistrationId()
                    . "'>" . $transferredRegistration->getFirstname() . ' ' . $transferredRegistration->getLastname() . '</a>. ';
            }
        }

        $lastBadgeID = 'fixme';

        $vars = [
            'registration' => $registration,
            'event' => $event,
            'registrationType' => $registrationType,
            'registrationStatus' => $registrationStatus,
            'history' => $registrationHistory,
            'badges' => $badges,
            'info' => $info,
            'lastBadgeId' => $lastBadgeID,
        ];

        return $this->render('registration/viewRegistration.html.twig', $vars);
    }

}
