<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Badge;

use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeStatus;
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\History;
use AppBundle\Entity\Registration;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class BadgeController extends Controller
{
    /**
     * @Route("/badge/status/{registrationId}/{badgeId}", name="badge_status")
     * @Security("has_role('ROLE_USER')")
     *
     *
     * @param String $registrationId RegistrationId for active registration
     * @param String $badgeId BadgeId for badge
     * @return Response
     */
    public function badgeStatus($registrationId, $badgeId)
    {
        $vars = [];

        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $vars['event'] = $event;

        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        $vars['registration'] = $registration;

        $badge = $this->getDoctrine()->getRepository(Badge::class)->find($badgeId);
        $vars['badge'] = $badge;

        return $this->render('badge/badgeStatus.html.twig', $vars);
    }


    /**
     * @Route("/badge/modify/{registrationId}/{badgeId}/{action}", name="badge_modify")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $registrationId RegistrationId for active registration
     * @param String $badgeId BadgeId for badge
     * @param String $action What are we doing to this badge?
     * @return Response
     * @throws ORMException
     */
    public function badgeModify(Request $request, $registrationId, $badgeId, $action) {
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $response = [];

        $error = false;
        $errorMessage = '';

        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        if (!$registration) {
            $error = true;
            $errorMessage = 'Registration ID was not valid';
        }

        $badge = $this->getDoctrine()->getRepository(Badge::class)->find($badgeId);
        if (!$error && !$badge) {
            $error = true;
            $errorMessage = 'Badge ID was not valid';
        }
        /** @var BadgeType $badgeType */
        $badgeType = $badge->getBadgeType();

        if ($badge->getRegistration()->getRegistrationId() != $registration->getRegistrationId()) {
            $error = true;
            $errorMessage = 'Registration ID did not match the Badge';
        }

        if (!$error) {
            $badgeStatus = null;
            $createNew = false;
            switch ($action) {
                case 'lost':
                    $badgeStatus = $this
                        ->getDoctrine()
                        ->getRepository(BadgeStatus::class)
                        ->getBadgeStatusFromStatus('LOST');
                    $createNew = true;
                    break;
                case 'revoked':
                    $badgeStatus = $this
                        ->getDoctrine()
                        ->getRepository(BadgeStatus::class)
                        ->getBadgeStatusFromStatus('REVOKED');
                    break;
                case 'new':
                case 'pickedup':
                    $badgeStatus = $this
                        ->getDoctrine()
                        ->getRepository(BadgeStatus::class)
                        ->getBadgeStatusFromStatus('PICKEDUP');
                    break;
                default:
                    break;
            }
            if ($badgeStatus) {
                $badge->setBadgestatus($badgeStatus);
                $entityManager->persist($badge);

                $registrationHistory = new History();
                $registrationHistory->setRegistration($registration);

                $text = "Updated {$badgeType->getDescription()} badge #{$badge->getNumber()} to status {$badgeStatus->getDescription()}.";


                if ($createNew) {
                    $newBadge = new Badge();
                    $newBadge->setBadgetype($badge->getBadgetype());
                    $newBadgeStatus = $this
                        ->getDoctrine()
                        ->getRepository(BadgeStatus::class)
                        ->getBadgeStatusFromStatus('PICKEDUP');
                    $newBadge->setBadgestatus($newBadgeStatus);
                    $badgeNumber = $this
                        ->getDoctrine()
                        ->getRepository(Badge::class)
                        ->generateNumber();
                    $newBadge->setNumber($badgeNumber);
                    $newBadge->setRegistration($registration);
                    $entityManager->persist($newBadge);

                    $text .= " Created a new badge of the same type with badge #{$badge->getNumber()}";
                }

                if ($request->request->has('note')) {
                    $text .= '<br><b>Comment: </b>' . $request->request->get('note');
                }
                $registrationHistory->setChangetext($text);
                $entityManager->persist($registrationHistory);
            }
            $entityManager->flush();
        }

        $response['success'] = !$error;
        $response['message'] = $errorMessage;

        return new JsonResponse($response);
    }
}
