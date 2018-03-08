<?php

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
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

        if (!$registration instanceof Registration) {
            return $this->redirectToRoute('listRegistrations');
        }

        $event = $registration->getEvent();
        $registrationType = $registration->getRegistrationType();
        $registrationStatus = $registration->getRegistrationStatus();
        $registrationHistory = $this->getDoctrine()->getRepository(History::class)->getHistoryFromRegistration($registration);
        $badges = $this->getDoctrine()->getRepository(Badge::class)->getBadgesFromRegistration($registration);

        $info = '';
        if ($registrationStatus->getActive()) {
            $info = $registrationStatus->getDescription();
            if ($registrationStatus->getStatus() == 'Transfered') {
                $transferredRegistration = $registration->getTransferredTo();
                $url = $this->generateUrl('viewRegistration', ['registrationId' => $transferredRegistration->getRegistrationId()]);
                $info .= " Transferred to <a href='$url'>" . $transferredRegistration->getFirstname()
                    . ' ' . $transferredRegistration->getLastname() . '</a>. ';
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
