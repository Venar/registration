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
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\Group;
use AppBundle\Entity\Registration;
use AppBundle\Entity\History;
use AppBundle\Entity\Registrationshirt;
use AppBundle\Entity\RegistrationStatus;
use AppBundle\Entity\RegistrationType;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RolloverController extends Controller
{
    /**
     * @Route("/registration/rollover/{registrationId}", name="rolloverRegistration")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return Response
     */
    public function rolloverInformation($registrationId)
    {
        $vars = [];
        $vars['error'] = '';
        $vars['errorMessage'] = '';

        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        $vars['registration'] = $registration;

        if (!$registration->getRegistrationStatus()->getActive()) {
            $vars['error'] = 'Registration is not active!';
            $vars['errorMessage'] = 'You cannot rollover an inactive registration!';
        }

        /** @var Badge[] $badges */
        $badges = $registration->getBadges();
        $vars['badges'] = $badges;

        $vars['nextYear'] = $registration->getEvent()->getYear() + 1;

        $nextEvent = $this
            ->getDoctrine()
            ->getRepository(Event::class)
            ->getEventFromYear($vars['nextYear']);
        if (!$nextEvent) {
            $vars['error'] = 'Next year not configured';
            $vars['errorMessage'] = 'You cannot rollover until the admin sets up a next event!';
        }

        return $this->render('registration/rollover.html.twig', $vars);
    }

    /**
     * @Route("registration/rollover/confirm/{registrationId}", name="rolloverRegistrationConfirm")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return Response
     * @throws ORMException
     */
    public function rolloverConfirm($registrationId) {
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $oldRegistration = $this
            ->getDoctrine()
            ->getRepository(Registration::class)
            ->find($registrationId);
        $registrationType = $this
            ->getDoctrine()
            ->getRepository(RegistrationType::class)
            ->getRegistrationTypeFromType('Rollover');
        $registrationStatusNew = $this
            ->getDoctrine()
            ->getRepository(RegistrationStatus::class)
            ->getRegistrationStatusFromStatus('New');
        $registrationStatusRollover = $this
            ->getDoctrine()
            ->getRepository(RegistrationStatus::class)
            ->getRegistrationStatusFromStatus('RolledOver');
        $registrationStatus = $oldRegistration->getRegistrationStatus();

        if (!$registrationStatus->getActive()) {
            $params = ['registrationId' => $registrationId];
            return $this->redirectToRoute('viewRegistration', $params);
        }

        $nextYear = $oldRegistration->getEvent()->getYear() + 1;
        $nextEvent = $this->getDoctrine()->getRepository(Event::class)->getEventFromYear($nextYear);
        if (!$nextEvent) {
            $params = ['registrationId' => $oldRegistration->getRegistrationId()];
            return $this->redirectToRoute('viewRegistration', $params);
        }

        $registration = new Registration();
        $registration->setEvent($nextEvent);
        $registration->setRegistrationStatus($registrationStatusNew);
        $registration->setRegistrationType($registrationType);
        $registration->setFirstName($oldRegistration->getFirstName());
        $registration->setMiddleName($oldRegistration->getMiddleName());
        $registration->setLastName($oldRegistration->getLastName());
        $registration->setBadgename($oldRegistration->getBadgeName());
        $registration->setEmail($oldRegistration->getEmail());
        $registration->setBirthday($oldRegistration->getBirthday());
        $registration->setAddress($oldRegistration->getAddress());
        $registration->setAddress2($oldRegistration->getAddress2());
        $registration->setCity($oldRegistration->getCity());
        $registration->setState($oldRegistration->getState());
        $registration->setZip($oldRegistration->getZip());
        $registration->setPhone($oldRegistration->getPhone());
        $registration->setContactNewsletter($oldRegistration->getContactNewsletter());
        $registration->setContactVolunteer($oldRegistration->getContactVolunteer());
        $number = $this->getDoctrine()->getRepository(Registration::class)->generateNumber($registration);
        $registration->setNumber($number);

        $entityManager->persist($registration);
        $entityManager->flush();

        $oldBadges = $oldRegistration->getBadges();
        foreach ($oldBadges as $oldBadge) {
            /** @var Badge $oldBadge */
            if ($oldBadge->getBadgeType()->getName() == 'Staff') {
                // We will not rollover a staff badge
                continue;
            }
            $badge = new Badge();
            $number = $this->getDoctrine()->getRepository(Badge::class)->generateNumber();
            $badge->setNumber($number);
            $badge->setBadgetype($oldBadge->getBadgetype());
            $badge->setBadgestatus($oldBadge->getBadgestatus());
            $badge->setRegistration($registration);
            $entityManager->persist($badge);
        }

        $oldRegistrationShirts = $oldRegistration->getRegistrationShirts();
        foreach ($oldRegistrationShirts as $oldRegistrationShirt) {
            /** @var RegistrationShirt $oldRegistrationShirt */
            $registrationShirt = new RegistrationShirt();
            $registrationShirt->setRegistration($registration);
            $registrationShirt->setShirt($oldRegistrationShirt->getShirt());
            $entityManager->persist($registrationShirt);
        }

        $oldHistory = '';
        $groups = $oldRegistration->getGroups();
        foreach ($groups as $group) {
            /** @var Group $group */
            $oldHistory = "Group Removed: {$group->getName()}<br>";
            $registration->removeGroup($group);
        }
        $entityManager->flush();

        $this->get('util_email')->generateAndSendConfirmationEmail($registration);

        $registrationHistory = new History();
        $registrationHistory->setRegistration($registration);
        $url = $this->generateUrl('viewRegistration', ['registrationId' => $oldRegistration->getRegistrationId()]);
        $history = " Transferred From <a href='$url'>"
            . $oldRegistration->getEvent()->getYear() . '</a>. <br>';
        $registrationHistory->setChangetext($history . '<br>Registration created from Rolled-over');
        $entityManager->persist($registrationHistory);

        $oldRegistration->setTransferredTo($registration);
        $oldRegistration->setRegistrationstatus($registrationStatusRollover);
        $entityManager->persist($oldRegistration);
        $entityManager->flush();

        $registrationHistory = new History();
        $registrationHistory->setRegistration($oldRegistration);
        $url = $this->generateUrl('viewRegistration', ['registrationId' => $registration->getRegistrationId()]);
        $oldHistory .= " Transferred To <a href='$url'>"
            . $registration->getEvent()->getYear() . '</a>. <br>';
        $registrationHistory->setChangetext($oldHistory . '<br>Registration Rolled-over');
        $entityManager->persist($registrationHistory);
        $entityManager->flush();

        $params = ['registrationId' => $registrationId];
        return $this->redirectToRoute('viewRegistration', $params);
    }
}
