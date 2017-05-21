<?php declare(strict_types=1);

namespace AppBundle\Controller\Registration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ViewRegistrationController extends Controller
{
    /**
     * @Route("/view_registration/{registrationID}", name="1")
     *
     * @param String $registrationID
     * @return Response
     */
    public function viewRegistrationPage($registrationID)
    {
        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationID);

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
                $info .= " Transferred to <a href='/view_registration/" . $transferredRegistration->getRegistrationId()
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
