<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationEmailController extends Controller
{
    /**
     * @Route("/registration/resend/{registrationId}", name="resendConfirmation")
     * @Security("has_role('ROLE_REGSTAFF')")
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
