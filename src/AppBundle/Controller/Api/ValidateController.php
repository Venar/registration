<?php declare(strict_types=1);

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ValidateController extends Controller
{
    /**
     * @Route("/api/validate/confirmation/{confirmation}/{lastname}")
     *
     * @param String $confirmation Confirmation Code for Registration
     * @param String $lastname Lastname that should match the Registration
     * @return Response
     */
    public function validateConfirmation($confirmation, $lastname)
    {
        $headers = [];
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $http_origin = $_SERVER['HTTP_ORIGIN'];
            if ($http_origin == "http://www.animedetour.com" || $http_origin == "http://animedetour.com") {
                $headers['Access-Control-Allow-Origin'] = $http_origin;
            }
        }

        $returnJson = array('status' => 'invalid', 'active' => false);

        $currentEvent = $this->get('repository_event')->getCurrentEvent();

        $registration = $this->get('repository_registration')->getFromConfirmation($confirmation, $currentEvent);
        if ($registration instanceof Registration
            && strtolower(trim($registration->getLastname())) == strtolower(trim($lastname))
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
