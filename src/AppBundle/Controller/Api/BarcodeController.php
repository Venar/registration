<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BarcodeController extends Controller
{
    /**
     * @Route("/api/barcode/validate", name="apiValidateBarcode")
     * @Route("/api/barcode/validate/", name="apiValidateBarcode_Slash")
     *
     * @param Request $request
     * @return Response
     */
    public function processBarcode(Request $request)
    {
        $response = ['valid' => false];
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $barcode = '';
        if ($request->query->has('barcode')) {
            $barcode = trim($request->query->get('barcode'));
        }
        if (substr($barcode, 0, 1) == '@') {
            $barcode = preg_replace( "/\r|\n/", "_", $barcode);
        }
        //Badge Example: $AD-B-K0001-4941-16cf2866d9
        $type = substr($barcode, 0, 5);
        $registration = null;
        switch($type) {
            case '$AD-B':
                $registrationNumber = substr($barcode, 6, 5);
                $badgeNumber = substr($barcode, 12, 4);
                $year = substr($barcode, 17, 2);
                $confirmationNumber = substr($barcode, 19);

                $registration = $this
                    ->getDoctrine()
                    ->getRepository(Registration::class)
                    ->findFromNumberAndConfirmation($registrationNumber, $badgeNumber, $confirmationNumber, $event);
                break;
            case '$AD-C':
                $confirmationNumber = substr($barcode, 6);

                $registration = $this
                    ->getDoctrine()
                    ->getRepository(Registration::class)
                    ->findFromConfirmation($confirmationNumber, $event);
                break;
            case '@__AN':
                $barcodeParts = explode('_',$barcode);
                $name = explode(' ', substr($barcodeParts[3], 3));
                $firstName = $name[0];
                $lastName = $name[-1];
                $birthday = substr($barcodeParts[4], 3);
                $birthdayYear = substr($birthday, 0, 4);
                $birthdayMonth = substr($birthday, 4, 2);
                $birthdayDay = substr($birthday, 6, 2);

                $registration = $this
                    ->getDoctrine()
                    ->getRepository(Registration::class)
                    ->findDriversLicenseInfo($firstName, $lastName,
                        $birthdayYear, $birthdayMonth, $birthdayDay, $event);
                break;
            default:
                break;
        }
        if ($registration instanceof Registration) {
            $response['valid'] = true;
            $response['registrationID'] = $registration->getRegistrationId();
        }

        return new JsonResponse($response);
    }
}
