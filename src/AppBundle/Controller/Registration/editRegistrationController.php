<?php declare(strict_types=1);

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Registrationhistory;
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

        $returnJson = array();
        $returnJson['success'] = false;

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
        if (!array_key_exists('Registration_ID', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Registration_ID was not set.';
        }
        if (!array_key_exists('regtype', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'regtype was not set.';
        }
        if (!array_key_exists('Birthday', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthday was not set.';
        }
        if (!array_key_exists('Birthyear', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthyear was not set.';
        }
        if (!array_key_exists('RegistrationType', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'RegistrationType was not set.';
        }
        $registrationType = RegistrationType::load_from_type($_REQUEST['RegistrationType']);
        if (!$registrationType instanceof RegistrationType) {
            $all_fields_sent = false;
            $returnJson['message'] = "RegistrationType '" . $_REQUEST['RegistrationType'] . "' didn't exist. Configuration Error.";
        }
        if (!array_key_exists('RegistrationStatus', $_REQUEST)) {
            $all_fields_sent = false;
            $returnJson['message'] = 'RegistrationStatus was not set.';
        }
        $registrationStatus = RegistrationStatus::load_from_status($_REQUEST['RegistrationStatus']);
        if (!$registrationStatus instanceof RegistrationStatus) {
            $all_fields_sent = false;
            $returnJson['message'] = "RegistrationStatus '" . $_REQUEST['RegistrationStatus'] . "' didn't exist. Configuration Error.";
        }
        foreach ($fields as $field => $fieldName) {
            if (!array_key_exists($field, $_REQUEST)) {
                $all_fields_sent = false;
                $returnJson['message'] = $fieldName . ' was not set.';
                break;
            }
        }

        $transferredFrom = null;
        if (array_key_exists('TransferredFrom', $_REQUEST)) {
            $transferredFrom = Registration::load($_REQUEST['TransferredFrom']);
        }

        $history = '';
        $event = Event::LoadSelectedYear();
        if ($all_fields_sent) {
            $registration = Registration::load($_REQUEST['Registration_ID']);

            if (!$registration instanceof Registration) {
                $registration = new Registration();

                $registration->Event_ID = $event->Event_ID;
                $registration->CreatedDate = date('Y-m-d H:i:s.u');
                $history .= 'Reg Type: ' . $registrationType->Name . '<br>';
                $history .= 'Reg Status: ' . $registrationStatus->Status . '<br>';
            } else {
                $oldRegType = RegistrationType::load($registration->RegistrationType_ID);
                if ($oldRegType->RegistrationType_ID != $registrationType->RegistrationType_ID) {
                    $history .= 'Reg Type: ' . $oldRegType->Name . ' => ' . $registrationType->Name . '<br>';
                }
                $oldRegStatus = RegistrationStatus::load($registration->RegistrationStatus_ID);
                if ($oldRegStatus->RegistrationStatus_ID != $registrationStatus->RegistrationStatus_ID) {
                    $history .= 'Reg Status: ' . $oldRegStatus->Status . ' => ' . $registrationStatus->Status . '<br>';
                }
            }
            $registration->RegistrationStatus_ID = $registrationStatus->RegistrationStatus_ID;
            $registration->RegistrationType_ID = $registrationType->RegistrationType_ID;

            if ($registrationType->Name != 'Group') {
                $RegistrationRegGroups = $registration->findAllRegistrationRegGroups();
                foreach ($RegistrationRegGroups as $RegistrationRegGroup) {
                    $tmpRegGroup = $RegistrationRegGroup->get_reggroup();
                    $history .= "Group Removed: {$tmpRegGroup->Name}<br>";
                    $RegistrationRegGroup->delete();
                }
            }

            $RegGroup = null;
            if ($registrationType->Name == 'Group'
                && array_key_exists('RegGroup_ID', $_REQUEST)
            ) {
                $RegGroup = RegGroup::load($_REQUEST['RegGroup_ID']);
            }

            foreach ($fields as $field => $fieldName) {
                if ($field == 'Birthyear') {
                    continue;
                }
                if ($field == 'Birthday') {
                    $tmpfield = $_REQUEST['Birthday'] . '/' . $_REQUEST['Birthyear'];
                    if (strtotime($registration->$field) != strtotime($tmpfield)) {
                        $history .= $field . ': ' . $registration->$field . ' => ' . $tmpfield . '<br>';
                    }
                    $registration->Birthday = $_REQUEST['Birthday'] . '/' . $_REQUEST['Birthyear'];
                    continue;
                }
                if ($registration->$field != $_REQUEST[$field]) {
                    $history .= $field . ': ' . $registration->$field . ' => ' . $_REQUEST[$field] . '<br>';
                }
                $registration->$field = $_REQUEST[$field];
            }

            $birthdate = (String)$_REQUEST['Birthday'] . '/' . $_REQUEST['Birthyear'];
            if (!strtotime($birthdate)) {
                $birthdate = str_replace('-', '/', $birthdate);
            }
            $registration->Birthday = date('Y-m-d H:i:s.u', strtotime($birthdate));
            if (strtotime($birthdate) === false) {
                $registration->Birthday = date('Y-m-d H:i:s.u');
            }

            $registration->contact_volunteer = '';
            if (array_key_exists('volunteer', $_REQUEST) && $_REQUEST['volunteer']) {
                $registration->contact_volunteer = 'true';
            }
            $registration->contact_newsletter = '';
            if (array_key_exists('newsletter', $_REQUEST) && $_REQUEST['newsletter']) {
                $registration->contact_newsletter = 'true';
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
            $Badges = $registration->find_all_badges();
            $to_delete = array();
            $badgetype_found = false;
            $regtype = $_REQUEST['regtype'];
            if ($regtype == 'ADREGSTANDARD') {
                if (strtotime($registration->Birthday) > strtotime($event->StartDate . " -18 years")) {
                    $regtype = 'MINOR';
                }
            }
            foreach ($Badges as $Badge) {
                /* @var $Badge Badge */
                $BadgeType = $Badge->get_badge_type();
                if (in_array($BadgeType->Name, $allow_one_badge_types) && $BadgeType->Name != $regtype) {
                    $to_delete[] = $Badge;
                    $history .= 'BadgeType: ' . $BadgeType->Name . ' => ' . $regtype . '<br>';
                }
                if ($BadgeType->Name == $regtype) {
                    $badgetype_found = true;
                }
            }
            if (!$registration->Registration_ID) {
                $registration->generate_number();
            }

            if ($transferredFrom instanceof Registration) {
                $registration->TransferredTo = $transferredFrom->Registration_ID;
                $history .= " Transferred From <a href='/view_registration/" . $transferredFrom->Registration_ID
                    . "'>" . $transferredFrom->FirstName . ' ' . $transferredFrom->LastName . '</a>. <br>';
            }

            $registration->save();

            if ($transferredFrom instanceof Registration) {
                $TransferredRegistrationStatus = RegistrationStatus::load_from_status('Transferred');
                $transferredFrom->RegistrationStatus_ID = $TransferredRegistrationStatus->RegistrationStatus_ID;
                $transferredFrom->save();

                $RegistrationRegGroups = $transferredFrom->findAllRegistrationRegGroups();
                foreach ($RegistrationRegGroups as $RegistrationRegGroup) {
                    $tmpRegGroup = $RegistrationRegGroup->get_reggroup();
                    $TransferredFromHistory = "Group Removed: {$tmpRegGroup->Name}<br>";
                    $RegistrationRegGroup->delete();
                }

                $RegistrationHistory = new RegistrationHistory();
                $RegistrationHistory->Registration_ID = $transferredFrom->Registration_ID;
                $transferredToText= "<br>Registration Transferred to "
                    ."<a href='/view_registration/{$registration->Registration_ID}'>"
                    ."{$registration->FirstName} {$registration->LastName}</a>";
                $RegistrationHistory->ChangeText = $TransferredFromHistory . $transferredToText;
                $RegistrationHistory->save();
            }

            if ($RegGroup instanceof RegGroup) {
                $RegGroups = $registration->find_all_reggroups();
                $group_found = false;
                foreach ($RegGroups as $OldRegGroup) {
                    /* @var RegGroup $OldRegGroup */
                    if ($OldRegGroup->RegGroup_ID == $RegGroup->RegGroup_ID) {
                        $group_found = true;
                    }
                }
                if (!$group_found) {
                    $RegistrationRegGroups = $registration->findAllRegistrationRegGroups();
                    foreach ($RegistrationRegGroups as $RegistrationRegGroup) {
                        $tmpRegGroup = $RegistrationRegGroup->get_reggroup();
                        $history .= "Group Removed: {$tmpRegGroup->Name}<br>";
                        $RegistrationRegGroup->delete();
                    }
                    $RegistrationRegGroup = new RegistrationRegGroup();
                    $RegistrationRegGroup->Registration_ID = $registration->Registration_ID;
                    $RegistrationRegGroup->RegGroup_ID = $RegGroup->RegGroup_ID;
                    $RegistrationRegGroup->save();

                    $history .= "Group Added: {$RegGroup->Name}<br>";
                }
            }

            if ($registration->Registration_ID && (count($to_delete) > 0 || !$badgetype_found)) {
                foreach ($to_delete as $Badge) {
                    /* @var $Badge Badge */
                    $Badge->delete();
                }

                $BadgeStatus = BadgeStatus::load_from_status('NEW');
                $BadgeType = BadgeType::load_from_type($regtype);
                $Badge = new Badge();
                $Badge->Registration_ID = $registration->Registration_ID;
                $Badge->BadgeType_ID = $BadgeType->BadgeType_ID;
                $Badge->BadgeStatus_ID = $BadgeStatus->BadgeStatus_ID;
                $Badge->generate_number();
                $history .= 'BadgeType: Added Badge Type: ' . $BadgeType->Name . '<br>';
                $Badge->save();
            }

            $registration->sendConfirmation();
        }

        if ($registration->Registration_ID) {
            $RegistrationHistory = new RegistrationHistory();
            $RegistrationHistory->Registration_ID = $registration->Registration_ID;

            if (array_key_exists('comments', $_REQUEST) && $_REQUEST['comments']) {
                if ($history) {
                    $history .= '<br><br>';
                }
                $history .= '<b>Comment:</b> ' . nl2br($_REQUEST['comments']);
            }

            $RegistrationHistory->ChangeText = $history;

            if ($history) {
                $RegistrationHistory->save();
            }

            $returnJson['success'] = true;
            $returnJson['message'] = 'Registration Updated!';
        }
        if ($registration instanceof Registration) {
            $returnJson['Registration_ID'] = $registration->Registration_ID;
            $returnJson['Number'] = $registration->Number;
        }
        $returnJson['Year'] = $event->Year;

        return new JsonResponse($returnJson);
    }
}
