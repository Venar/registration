<?php declare(strict_types=1);

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationhistory;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registrationshirt;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class editRegistrationController extends Controller
{
    /**
     * @Route("/editregistration/")
     * @Route("/editregistration/{registrationID}")
     * @Route("/editregistration/{registrationID}/{regGroupId}")
     * @Route("/editregistration/{registrationID}/{regGroupId}/{transferredFrom}")
     *
     * @param String $registrationID
     * @param String $regGroupId
     * @param String $transferredFrom
     * @return Response
     */
    public function editRegistrationPage($registrationID = null, $regGroupId = null, $transferredFrom = null)
    {
        $event = $this->get('repository_event')->getSelectedEvent();
        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationID);
        $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);

        $group = $this->get('repository_reggroup')->getFromReggroupId($regGroupId);
        if ($registration && !$group) {
            // If its a valid registration, look for an existing group
            $group = $this->get('repository_reggroup')->getRegGroupFromRegistration($registration);
            if ($group) {
                $regGroupId = $group->getReggroupId();
            }
        }

        $transferredRegistration = $this->get('repository_registration')->getFromRegistrationId($transferredFrom);
        $transferredBadges = null;
        if ($transferredRegistration) {
            $transferredBadges = $this->get('repository_badge')->getBadgesFromRegistration($transferredRegistration);
        }

        $currentBadgeTypes = [];
        foreach ($badges as $badge) {
            $currentBadgeTypes[] = $badge->getBadgetype()->getName();
        }

        $vars = [
            'event' => $event,
            'registrationTypes' => $this->get('repository_registrationtype')->findAll(),
            'regGroups' => $this->get('repository_reggroup')->findAll(),
            'registrationStatuses' => $this->get('repository_registrationstatus')->findAll(),
            'registration' => $registration,
            'isStaff' => $this->get('repository_badge')->isStaff($registration),
            'currentBadgeTypes' => $currentBadgeTypes,
            'badges' => $badges,
            'group' => $group,
            'transferredRegistration' => $transferredRegistration,
            'transferredBadges' => $transferredBadges,
        ];

        return $this->render('registration/editRegistration.html.twig', $vars);
    }

    /**
     * @Route("/getregshirtlist/{registrationID}")
     *
     * @param String $registrationID
     * @return JsonResponse
     */
    public function getRegistrationShirts($registrationID)
    {
        $arrayShirts = [];
        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationID);
        if ($registration) {
            $registrationShirts = $this->get('repository_registrationshirt')->getRegistrationShirtsFromRegistration($registration);

            foreach ($registrationShirts as $registrationShirt) {
                $tmp = array();
                $tmp['RegistrationShirt_ID'] = $registrationShirt->getRegistrationshirtId();
                $tmp['shirt_type'] = $registrationShirt->getShirt()->getShirttype();
                $tmp['shirt_size'] = $registrationShirt->getShirt()->getShirtsize();
                $arrayShirts[] = $tmp;
            }
        }

        return new JsonResponse($arrayShirts);
    }

    /**
     * @Route("/ajaxstaffmodify/{registrationID}/{action}")
     *
     * @param String $registrationID
     * @return JsonResponse
     */
    public function modifyStaffStatus($registrationID, $action)
    {
        $returnData = array();
        $returnData['success'] = false;
        $returnData['message'] = 'Invalid Registration ID. Save your badge first.';

        $entityManager = $this->get('doctrine.orm.entity_manager');

        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationID);
        if ($registration && ($action == 'add' || $action == 'remove')) {
            $badgeType = $this->get('repository_badgetype')->getBadgeTypeFromType('STAFF');
            if ($action == 'remove') {
                $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
                foreach ($badges as $badge) {
                    if ($badge->getBadgetype()->getBadgetypeId() == $badgeType->getBadgetypeId()) {
                        $entityManager->remove($badge);
                        $returnData['success'] = true;
                        $returnData['message'] = 'Staff badge deleted';
                    }
                }
            } elseif ($action == 'add') {
                $badgeStatus = $this->get('repository_badgestatus')->getBadgeStatusFromStatus('NEW');
                $badge = new Badge();
                $badge->setRegistration($registration);
                $badge->setBadgetype($badgeType);
                $badge->setBadgestatus($badgeStatus);
                $badge->setNumber($this->get('repository_badge')->generateNumber());
                $entityManager->persist($badge);
                $returnData['success'] = true;
                $returnData['message'] = 'Staff badge created';
            }
        }

        if ($registration->getRegistrationId() && $returnData['success']) {
            $registrationHistory = new Registrationhistory();
            $registrationHistory->setRegistration($registration);
            $registrationHistory->setChangetext($returnData['message']);
            $entityManager->persist($registrationHistory);

            $entityManager->flush();
        }

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/editregshirtlist/{registrationID}/{action}")
     *
     * @param Request $request
     * @param String $registrationID
     * @param String $action
     * @return JsonResponse
     */
    public function modifyTShirts(Request $request, $registrationID, $action)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $returnData = array();
        $returnData['success'] = false;
        $returnData['message'] = '';

        $registration = $this->get('repository_registration')->getFromRegistrationId($registrationID);
        if (!$registration) {
            $returnData['message'] = 'Invalid Registration ID';

            return new JsonResponse($returnData);
        }

        if ($action == 'add') {
            if ($request->query->has('shirt_type')
                && $request->query->has('shirt_size')
            ) {
                $shirtType = $request->query->get('shirt_type');
                $shirtSize = $request->query->get('shirt_size');

                $shirt = $this->get('repository_shirt')->getShirtFromTypeAndSize($shirtType, $shirtSize);
                if (!$shirt) {
                    $returnData['message'] = 'Error: Invalid Type/Size';

                    return new JsonResponse($returnData);
                }

                $registrationShirt = new Registrationshirt();
                $registrationShirt->setRegistration($registration);
                $registrationShirt->setShirt($shirt);
                $entityManager->persist($registrationShirt);
                $entityManager->flush();

                if ($registrationShirt->getRegistrationshirtId()) {
                    $returnData['success'] = true;
                    $returnData['message'] = 'Added shirt size ' . $shirtType . ' ' . $shirtSize . ' to registration';
                    $returnData['RegistrationShirt_ID'] = $registrationShirt->getRegistrationshirtId();
                }
            }
        } else if ($action == 'delete') {
            if ($request->query->has('RegistrationShirt_ID')) {
                $registrationShirt_ID = $request->query->get('RegistrationShirt_ID');
                $registrationShirt = $this->get('repository_registrationshirt')->getFromRegistrationShirtId($registrationShirt_ID);
                $shirt = $registrationShirt->getShirt();

                $entityManager->remove($registrationShirt);
                $entityManager->flush();

                $returnData['message'] = 'Deleted shirt size ' . $shirt->getShirttype() . ' ' . $shirt->getShirtsize() . ' from registration';
                $returnData['success'] = true;
            }
        }

        if ($registration->getRegistrationId() && $returnData['success']) {
            $registrationHistory = new RegistrationHistory();
            $registrationHistory->setRegistration($registration);
            $registrationHistory->setChangetext($returnData['message']);
            $entityManager->persist($registrationHistory);

            $entityManager->flush();
        }

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/ajaxeditregistration")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditRegistration(Request $request) : JsonResponse
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $event = $this->get('repository_event')->getSelectedEvent();

        $returnJson = array();
        $returnJson['success'] = false;
        $returnJson['Year'] = $event->getYear();

        $all_fields_sent = true;
        $fields = array(
            'LastName' => 'last name',
            'FirstName' => 'first name',
            'MiddleName' => 'middle name',
            'Address' => 'address',
            'Address2' => 'address2',
            'City' => 'city',
            'State' => 'state',
            'Zip' => 'zip',
            'Phone' => 'phone number',
            'Email' => 'e-mail address',
            'BadgeName' => 'badge name',
            'Birthday' => 'birthday',
            'Birthyear' => 'birth year',
        );
        if (!$request->request->has('Registration_ID')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Registration_ID was not set.';
        }
        if (!$request->request->has('regtype')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'regtype was not set.';
        }
        if (!$request->request->has('Birthday')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthday was not set.';
        }
        if (!$request->request->has('Birthyear')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthyear was not set.';
        }
        if (!$request->request->has('RegistrationType')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'RegistrationType was not set.';
        }
        if (!$request->request->has('RegistrationStatus')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'RegistrationStatus was not set.';
        }

        $registrationType = $this->get('repository_registrationtype')
            ->getRegistrationTypeFromType($request->request->get('RegistrationType'));
        if (!$registrationType) {
            $all_fields_sent = false;
            $returnJson['message'] = "RegistrationType '{$request->request->get('RegistrationType')}' didn't exist. Configuration Error.";
        }

        $registrationStatus = $this->get('repository_registrationstatus')
            ->getRegistrationStatusFromStatus($request->request->get('RegistrationStatus'));
        if (!$registrationStatus) {
            $all_fields_sent = false;
            $returnJson['message'] = "RegistrationStatus '{$request->request->get('RegistrationStatus')}' didn't exist. Configuration Error.";
        }
        foreach ($fields as $field => $fieldName) {
            if (!$request->request->has($field)) {
                $all_fields_sent = false;
                $returnJson['message'] = $fieldName . ' was not set.';
                break;
            }
        }

        $transferredFrom = null;
        if ($request->request->has('TransferredFrom')) {
            $transferredFrom = $this->get('repository_registration')
                ->getFromRegistrationId($request->request->get('RegistrationStatus'));
        }

        $history = '';
        if ($all_fields_sent) {
            $registration = $this->get('repository_registration')
                ->getFromRegistrationId($request->request->get('Registration_ID'));

            if (!$registration) {
                $registration = new Registration();

                $registration->setEvent($event);
                $history .= 'Reg Type: ' . $registrationType->getName() . '<br>';
                $history .= 'Reg Status: ' . $registrationStatus->getStatus() . '<br>';
            } else {
                $oldRegType = $registration->getRegistrationtype();
                if ($oldRegType->getRegistrationtypeId() != $registrationType->getRegistrationtypeId()) {
                    $history .= 'Reg Type: ' . $oldRegType->getName() . ' => ' . $registrationType->getName() . '<br>';
                }
                $oldRegStatus = $registration->getRegistrationstatus();
                if ($oldRegStatus->getRegistrationstatusId() != $registrationStatus->getRegistrationstatusId()) {
                    $history .= 'Reg Status: ' . $oldRegStatus->getStatus() . ' => ' . $registrationStatus->getStatus() . '<br>';
                }
            }
            $registration->setRegistrationstatus($registrationStatus);
            $registration->setRegistrationtype($registrationType);

            if ($registrationType->getName() != 'Group') {
                $registrationRegGroups = $this->get('repository_registrationreggroup')
                    ->getRegistrationRegGroupFromRegistration($registration);
                foreach ($registrationRegGroups as $registrationRegGroup) {
                    $tmpRegGroup = $registrationRegGroup->getReggroup();
                    $history .= "Group Removed: {$tmpRegGroup->getName()}<br>";
                    $entityManager->remove($registrationRegGroup);
                }
                $entityManager->flush();
            }

            $regGroup = null;
            if ($registrationType->getName() == 'Group'
                && $request->request->has('RegGroup_ID')
            ) {
                $regGroup = $this->get('repository_reggroup')->getFromReggroupId($request->request->get('RegGroup_ID'));
            }

            foreach ($fields as $field => $fieldName) {
                if ($field == 'Birthyear') {

                    continue;
                }
                if ($field == 'Birthday') {
                    $tmpfield = $request->request->get('Birthday') . '/' . $request->request->get('Birthyear');
                    if (!strtotime($tmpfield)) {
                        $tmpfield = str_replace('-', '/', $tmpfield);
                    }
                    $newDate = new \DateTime($tmpfield);
                    $oldDate = $registration->getBirthday()->format('m/d/y');
                    if ($oldDate != $newDate->format('m/d/y')) {
                        $history .= "$field: $oldDate => {$newDate->format('m/d/y')}<br>";
                    }
                    $registration->setBirthday($newDate);

                    continue;
                }
                $value = $request->request->get($field);
                $fieldLowerSet = 'set'.ucfirst(strtolower($field));
                $fieldLowerGet = 'get'.ucfirst(strtolower($field));
                if ($registration->$fieldLowerGet() != $_REQUEST[$field]) {
                    $history .= "$field: {$registration->$fieldLowerGet()} => $value<br>";
                }
                $registration->$fieldLowerSet($value);
            }

            $registration->setContactVolunteer('');
            if ($request->request->has('volunteer') && $request->request->get('volunteer')) {
                $registration->setContactVolunteer('true');
            }
            $registration->setContactNewsletter('');
            if ($request->request->has('newsletter') && $request->request->get('newsletter')) {
                $registration->setContactNewsletter('true');
            }

            $allow_one_badge_types = array(
                'ADREGSTANDARD',
                'MINOR',
                'ADREGSPONSOR',
                'ADREGCOMMSPONSOR',
                'GUEST',
                'VENDOR',
                'EXHIBITOR'
            );

            $toDelete = [];
            $badgetypeFound = false;
            $regtype = $request->request->get('regtype');
            if ($regtype == 'ADREGSTANDARD') {
                if (strtotime($registration->getBirthday()) > strtotime($event->getStartdate() . " -18 years")) {
                    $regtype = 'MINOR';
                }
            }

            $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
            foreach ($badges as $badge) {
                $badgeType = $badge->getBadgetype();
                if (in_array($badgeType->getName(), $allow_one_badge_types) && $badgeType->getName() != $regtype) {
                    $toDelete[] = $badge;
                    $history .= "BadgeType: {$badgeType->getName()} => $regtype<br>";
                }
                if ($badgeType->getName() == $regtype) {
                    $badgetypeFound = true;
                }
            }
            if (!$registration->getRegistrationId()) {
                $registration->setNumber($this->get('repository_registration')->generateNumber($registration));
            }

            if ($transferredFrom) {
                $registration->setTransferedto($transferredFrom->getRegistrationId());
                $history .= " Transferred From <a href='/view_registration/" . $transferredFrom->getRegistrationId()
                    . "'>" . $transferredFrom->getFirstname() . ' ' . $transferredFrom->getLastname() . '</a>. <br>';
            }

            $entityManager->persist($registration);
            $entityManager->flush();

            if ($transferredFrom) {
                $transferredRegistrationStatus = $this->get('repository_registrationstatus')
                    ->getRegistrationStatusFromStatus('Transferred');
                $transferredFrom->setRegistrationstatus($transferredRegistrationStatus->RegistrationStatus_ID);
                $entityManager->persist($transferredFrom);

                $transferredFromHistory = '';
                $registrationRegGroups = $this->get('repository_registrationreggroup')
                    ->getRegistrationRegGroupFromRegistration($transferredFrom);
                foreach ($registrationRegGroups as $registrationRegGroup) {
                    $tmpRegGroup = $registrationRegGroup->getReggroup();
                    $transferredFromHistory .= "Group Removed: {$tmpRegGroup->getName()}<br>";
                    $entityManager->remove($registrationRegGroup);
                }

                $registrationHistory = new RegistrationHistory();
                $registrationHistory->setRegistration($transferredFrom);
                $transferredToText= "<br>Registration Transferred to "
                    ."<a href='/view_registration/{$registration->getRegistrationId()}'>"
                    ."{$registration->getFirstname()} {$registration->getLastname()}</a>";
                $registrationHistory->setChangetext($transferredFromHistory . $transferredToText);
                $entityManager->persist($registrationHistory);
                $entityManager->flush();
            }

            if ($regGroup) {
                $oldRegistrationRegGroups = $this->get('repository_registrationreggroup')->getRegistrationRegGroupFromRegistration($registration);
                $groupFound = false;
                foreach ($oldRegistrationRegGroups as $oldRegistrationRegGroup) {
                    if ($oldRegistrationRegGroup->getReggroup()->getReggroupId() == $regGroup->getReggroupId()) {
                        $groupFound = true;
                    }
                }
                if (!$groupFound) {
                    foreach ($oldRegistrationRegGroups as $oldRegistrationRegGroup) {
                        $tmpRegGroup = $oldRegistrationRegGroup->getReggroup();
                        $history .= "Group Removed: {$tmpRegGroup->getName()}<br>";
                        $entityManager->remove($oldRegistrationRegGroup);
                    }
                    $registrationRegGroup = new Registrationreggroup();
                    $registrationRegGroup->setRegistration($registration);
                    $registrationRegGroup->setReggroup($regGroup);
                    $entityManager->persist($registrationRegGroup);

                    $history .= "Group Added: {$regGroup->getName()}<br>";
                    $entityManager->flush();
                }
            }

            if ($registration->getRegistrationId() && (count($toDelete) > 0 || !$badgetypeFound)) {
                foreach ($toDelete as $badge) {
                    $entityManager->remove($badge);
                }

                $badgeStatus = $this->get('repository_badgestatus')->getBadgeStatusFromStatus('NEW');
                $badgeType = $this->get('repository_badgetype')->getBadgeTypeFromType($regtype);
                $badge = new Badge();
                $badge->setRegistration($registration);
                $badge->setBadgetype($badgeType);
                $badge->setBadgestatus($badgeStatus);
                $badge->setNumber($this->get('repository_badge')->generateNumber());
                $history .= "BadgeType: Added Badge Type: {$badgeType->getName()}<br>";
                $entityManager->persist($badge);
                $entityManager->flush();
            }

            $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
            $this->get('repository_registration')->sendConfirmationEmail($registration, $badges);

            $registrationHistory = new RegistrationHistory();
            $registrationHistory->setRegistration($registration);

            if ($request->request->has('comments') && $request->request->get('comments')) {
                if ($history) {
                    $history .= '<br><br>';
                }
                $history .= '<b>Comment:</b> ' . nl2br($request->request->get('comments'));
            }

            $registrationHistory->setChangetext($history);

            if ($history) {
                $entityManager->persist($registrationHistory);
                $entityManager->flush();
            }

            $returnJson['success'] = true;
            $returnJson['message'] = 'Registration Updated!';

            $returnJson['Registration_ID'] = $registration->getRegistrationId();
            $returnJson['Number'] = $registration->getNumber();
        }

        return new JsonResponse($returnJson);
    }
}
