<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ValidateController extends Controller
{
    /**
     * @Route("/api/validate/confirmation/{confirmation}/{lastName}", name="api_validate_confirmation")
     *
     * @param String $confirmation Confirmation Code for Registration
     * @param String $lastName Last name that should match the Registration
     * @return Response
     */
    public function validateConfirmation($confirmation, $lastName)
    {
        $headers = [];
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $http_origin = $_SERVER['HTTP_ORIGIN'];
            if ($http_origin == "https://www.animedetour.com" || $http_origin == "https://animedetour.com") {
                $headers['Access-Control-Allow-Origin'] = $http_origin;
            }
        }

        $returnJson = array('status' => 'invalid', 'active' => false);

        $currentEvent = $this->getDoctrine()->getRepository(Event::class)->getCurrentEvent();

        $registration = $this->getDoctrine()
            ->getRepository(Registration::class)
            ->getFromConfirmation($confirmation, $currentEvent);
        if ($registration instanceof Registration
            && strtolower(trim($registration->getLastname())) == strtolower(trim($lastName))
        ) {
            $returnJson['status'] = 'inactive';
            $returnJson['active'] = false;
            $registrationStatus = $registration->getRegistrationstatus();
            if ($registrationStatus->getActive()) {
                $returnJson['status'] = 'active';
                $returnJson['active'] = true;
            }
        }

        return new Response(
            json_encode($returnJson),
            200,
            $headers
        );
    }
}
