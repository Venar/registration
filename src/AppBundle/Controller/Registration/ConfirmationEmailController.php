<?php

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationEmailController extends Controller
{
    /**
     * @Route("/registration/resend/{registrationId}", name="resendConfirmation")
     *
     * @param String $registrationId
     * @return Response
     */
    public function resendEmail($registrationId)
    {
        $registration = null;
        if ($registrationId) {
            $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);

            $this->get('util_email')->generateAndSendConfirmationEmail($registration);
        }

        if (!$registration) {
            $this->redirectToRoute('listRegistrations');
        }

        $vars['registration'] = $registration;

        return $this->render(':registration:confirmationSent.html.twig', $vars);
    }
}
