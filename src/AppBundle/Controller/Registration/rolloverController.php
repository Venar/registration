<?php

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationhistory;
use AppBundle\Entity\Registrationshirt;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class rolloverController extends Controller
{
    /**
     * @Route("/registration/rollover/{registrationId}")
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

        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationId);
        $vars['registration'] = $registration;

        if (!$registration->getRegistrationstatus()->getActive()) {
            $vars['error'] = 'Registration is not active!';
            $vars['errorMessage'] = 'You cannot rollover an inactive registration!';
        }

        $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
        $vars['badges'] = $badges;

        $vars['nextYear'] = $registration->getEvent()->getYear() + 1;

        $nextEvent = $this->get('repository_event')->getEventFromYear($vars['nextYear']);
        if (!$nextEvent) {
            $vars['error'] = 'Next year not configured';
            $vars['errorMessage'] = 'You cannot rollover until the admin sets up a next event!';
        }

        return $this->render('registration/rollover.html.twig', $vars);
    }

    /**
     * @Route("registration/rollover/confirm/{registrationId}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return Response
     */
    public function rolloverConfirm($registrationId) {
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $oldRegistration = $this->get('repository_registration')->getFromRegistrationId($registrationId);
        $registrationType = $this->get('repository_registrationtype')->getRegistrationTypeFromType('Rollover');
        $registrationStatusNew = $this->get('repository_registrationstatus')->getRegistrationStatusFromStatus('New');
        $registrationStatusRollover = $this->get('repository_registrationstatus')->getRegistrationStatusFromStatus('RolledOver');
        $registrationStatus = $oldRegistration->getRegistrationstatus();

        if (!$registrationStatus->getActive()) {
            $params = ['registrationId' => $registrationId];
            return $this->redirectToRoute('app_registration_viewregistration_viewregistrationpage', $params);
        }

        $nextYear = $oldRegistration->getEvent()->getYear() + 1;
        $nextEvent = $this->get('repository_event')->getEventFromYear($nextYear);
        if (!$nextEvent) {
            $params = ['registrationId' => $oldRegistration->getRegistrationId()];
            return $this->redirectToRoute('app_registration_rollover_rolloverinformation', $params);
        }

        $registration = new Registration();
        $registration->setEvent($nextEvent);
        $registration->setRegistrationstatus($registrationStatusNew);
        $registration->setRegistrationtype($registrationType);
        $registration->setFirstname($oldRegistration->getFirstname());
        $registration->setMiddlename($oldRegistration->getMiddlename());
        $registration->setLastname($oldRegistration->getLastname());
        $registration->setBadgename($oldRegistration->getBadgename());
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
        $registration->setNumber($this->get('repository_registration')->generateNumber($registration));

        $entityManager->persist($registration);
        $entityManager->flush();

        $oldBadges = $this->get('repository_badge')->getBadgesFromRegistration($oldRegistration);
        foreach ($oldBadges as $oldBadge) {
            if ($oldBadge->getBadgetype()->getName() == 'Staff') {
                // We will not rollover a staff badge
                continue;
            }
            $badge = new Badge();
            $badge->setNumber($this->get('repository_badge')->generateNumber());
            $badge->setBadgetype($oldBadge->getBadgetype());
            $badge->setBadgestatus($oldBadge->getBadgestatus());
            $badge->setRegistration($registration);
            $entityManager->persist($badge);
        }

        $oldShirts = $this->get('repository_shirt')->getShirtsFromRegistration($oldRegistration);
        foreach ($oldShirts as $oldShirt) {
            $registrationShirt = new RegistrationShirt();
            $registrationShirt->setRegistration($registration);
            $registrationShirt->setShirt($oldShirt);
            $entityManager->persist($registrationShirt);
        }

        $oldHistory = '';
        $registrationRegGroups = $this->get('repository_registrationreggroup')->getRegistrationRegGroupFromRegistration($oldRegistration);
        foreach ($registrationRegGroups as $registrationRegGroup) {
            $oldHistory = "Group Removed: {$registrationRegGroup->getReggroup()->getName()}<br>";
            $entityManager->remove($registrationRegGroup);
        }
        $entityManager->flush();

        $newBadges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
        $this->get('repository_registration')->sendConfirmationEmail($registration, $newBadges);

        $registrationHistory = new RegistrationHistory();
        $registrationHistory->setRegistration($registration);
        $history = " Transferred From <a href='/registration/view/{$oldRegistration->getRegistrationId()}'>"
            . $oldRegistration->getEvent()->getYear() . '</a>. <br>';
        $registrationHistory->setChangetext($history . '<br>Registration created from Rolled-over');
        $entityManager->persist($registrationHistory);

        $oldRegistration->setTransferedto($registration);
        $oldRegistration->setRegistrationstatus($registrationStatusRollover);
        $entityManager->persist($oldRegistration);
        $entityManager->flush();

        $registrationHistory = new RegistrationHistory();
        $registrationHistory->setRegistration($oldRegistration);
        $oldHistory .= " Transferred To <a href='/registration/view/{$registration->getRegistrationId()}'>"
            . $registration->getEvent()->getYear() . '</a>. <br>';
        $registrationHistory->setChangetext($oldHistory . '<br>Registration Rolled-over');
        $entityManager->persist($registrationHistory);
        $entityManager->flush();

        $params = ['registrationId' => $registrationId];
        return $this->redirectToRoute('app_registration_viewregistration_viewregistrationpage', $params);
    }
}
