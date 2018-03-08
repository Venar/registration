<?php

namespace AppBundle\Controller\Registration;

use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeStatus;
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\Extra;
use AppBundle\Entity\Group;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationextra;
use AppBundle\Entity\History;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registrationshirt;
use AppBundle\Entity\RegistrationStatus;
use AppBundle\Entity\RegistrationType;
use AppBundle\Entity\Shirt;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class editRegistrationController extends Controller
{

    /**
     * @Route("/registration/transfer/{transferredFrom}", name="registrationTransfer")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $transferredFrom
     * @return Response
     */
    public function registrationTransfer($transferredFrom)
    {
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($transferredFrom);

        if (!$registration instanceof Registration) {
            return $this->redirectToRoute('listRegistrations');
        }

        if (!$registration->getRegistrationStatus()->getActive()) {
            $params = ['registrationId' => $registration->getRegistrationId()];
            return $this->redirectToRoute('app_registration_editregistration_editregistrationpage', $params);
        }

        return $this->editRegistrationPage(null, null, $transferredFrom);
    }

    /**
     * @Route("/registration/group/add/{groupId}", name="registrationAddToGroup")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $groupId
     * @return Response
     */
    public function registrationGroup($groupId)
    {
        $group = $this->getDoctrine()->getRepository(Group::class)->find($groupId);

        if (!$group) {
            return $this->redirectToRoute('listRegistrations');
        }

        return $this->editRegistrationPage(null, $groupId);
    }

    /**
     * @Route("/registration/edit/", name="actionEditRegistration")
     * @Route("/registration/edit/{registrationId}", name="actionEditRegistration")
     * @Route("/registration/edit/{registrationId}/{regGroupId}")
     * @Route("/registration/edit/{registrationId}/{regGroupId}/{transferredFrom}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @param String $groupId
     * @param String $transferredFrom
     * @return Response
     */
    public function editRegistrationPage($registrationId = null, $groupId = null, $transferredFrom = null)
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        $badges = $this->getDoctrine()->getRepository(Badge::class)->getBadgesFromRegistration($registration);

        $group = null;
        if ($groupId) {
            $group = $this->getDoctrine()->getRepository(Group::class)->find($groupId);
        }
        if ($registration && !$group) {
            // If its a valid registration, look for an existing group
            $group = $this->getDoctrine()->getRepository(Group::class)->getGroupFromRegistration($registration);
        }

        $transferredRegistration = null;
        if ($transferredRegistration) {
            $transferredRegistration = $this->getDoctrine()->getRepository(Registration::class)->find($transferredFrom);
        }
        $transferredBadges = null;
        if ($transferredRegistration) {
            $transferredBadges = $this->getDoctrine()->getRepository(Badge::class)->getBadgesFromRegistration($transferredRegistration);
        }

        $currentBadgeTypes = [];
        foreach ($badges as $badge) {
            $currentBadgeTypes[] = $badge->getBadgetype()->getName();
        }

        /** @var RegistrationType[] $registrationTypes */
        $registrationTypes = $this->getDoctrine()->getRepository(RegistrationType::class)->findAll();
        $selectedType = null;
        if ($group) {
            $selectedType = $this->getDoctrine()->getRepository(RegistrationType::class)->getRegistrationTypeFromType('Group');
        } elseif ($registration) {
            $selectedType = $registration->getRegistrationtype();
        }

        /** @var Extra[] $registrationTypes */
        $extras = $this->getDoctrine()->getRepository(Extra::class)->findAll();
        /** @var Group[] $registrationTypes */
        $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        $registrationStatuses = $this->getDoctrine()->getRepository(RegistrationStatus::class)->findAll();

        $vars = [
            'event' => $event,
            'registrationTypes' => $registrationTypes,
            'selectedType' => $selectedType,
            'group' => $group,
            'groups' => $groups,
            'registrationStatuses' => $registrationStatuses,
            'registration' => $registration,
            'isStaff' => $this->getDoctrine()->getRepository(Badge::class)->isStaff($registration),
            'currentBadgeTypes' => $currentBadgeTypes,
            'badges' => $badges,
            'transferredRegistration' => $transferredRegistration,
            'transferredBadges' => $transferredBadges,
            'extras' => $extras,
        ];

        return $this->render('registration/editRegistration.html.twig', $vars);
    }

    /**
     * @Route("/getregshirtlist/{registrationId}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return JsonResponse
     */
    public function getRegistrationShirts($registrationId)
    {
        $arrayShirts = [];
        $registration = null;
        if ($registrationId) {
            $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        }
        if ($registration instanceof Registration) {
            $shirts = $registration->getShirts();

            foreach ($shirts as $shirt) {
                $tmp = array();
                $tmp['shirt_type'] = $shirt->getType();
                $tmp['shirt_size'] = $shirt->getSize();
                $arrayShirts[] = $tmp;
            }
        }

        return new JsonResponse($arrayShirts);
    }

    /**
     * @Route("/ajaxstaffmodify/{registrationId}/{action}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return JsonResponse
     */
    public function modifyStaffStatus($registrationId, $action)
    {
        $returnData = array();
        $returnData['success'] = false;
        $returnData['message'] = 'Invalid Registration ID. Save your badge first.';

        $entityManager = $this->getDoctrine()->getManager();

        $registration = $entityManager->getRepository(Registration::class)->find($registrationId);
        if ($registration && ($action == 'add' || $action == 'remove')) {
            $badgeType = $entityManager->getRepository(BadgeType::class)->getBadgeTypeFromType('STAFF');
            if ($action == 'remove') {
                $badges = $entityManager->getRepository(Badge::class)->getBadgesFromRegistration($registration);
                foreach ($badges as $badge) {
                    if ($badge->getBadgetype()->getBadgetypeId() == $badgeType->getBadgetypeId()) {
                        $entityManager->remove($badge);
                        $returnData['success'] = true;
                        $returnData['message'] = 'Staff badge deleted';
                    }
                }
            } elseif ($action == 'add') {
                $badgeStatus = $entityManager->getRepository(BadgeStatus::class)->getBadgeStatusFromStatus('NEW');
                $badge = new Badge();
                $badge->setRegistration($registration);
                $badge->setBadgetype($badgeType);
                $badge->setBadgestatus($badgeStatus);
                $badge->setNumber($entityManager->getRepository(Badge::class)->generateNumber());
                $entityManager->persist($badge);
                $returnData['success'] = true;
                $returnData['message'] = 'Staff badge created';
            }
        }

        if ($registration->getRegistrationId() && $returnData['success']) {
            $registrationHistory = new History();
            $registrationHistory->setRegistration($registration);
            $registrationHistory->setChangetext($returnData['message']);
            $entityManager->persist($registrationHistory);

            $entityManager->flush();
        }

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/editregshirtlist/{registrationId}/{action}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $registrationId
     * @param String $action
     * @return JsonResponse
     */
    public function modifyTShirts(Request $request, $registrationId, $action)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $returnData = array();
        $returnData['success'] = false;
        $returnData['message'] = '';

        $registration = $entityManager->getRepository(Registration::class)->find($registrationId);
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

                $shirt = $entityManager->getRepository(Shirt::class)->getShirtFromTypeAndSize($shirtType, $shirtSize);
                if (!$shirt) {
                    $returnData['message'] = 'Error: Invalid Type/Size';

                    return new JsonResponse($returnData);
                }

                $registration->addShirt($shirt);

                $entityManager->flush();

                /**
                 * FIXME:
                if ($registrationShirt->getRegistrationshirtId()) {
                    $returnData['success'] = true;
                    $returnData['message'] = 'Added shirt size ' . $shirtType . ' ' . $shirtSize . ' to registration';
                    $returnData['RegistrationShirt_ID'] = $registrationShirt->getRegistrationshirtId();
                }
                 */
            }
        } else if ($action == 'delete') {
            if ($request->query->has('RegistrationShirt_ID')) {
                /**
                 * FIXME:
                $registrationShirt_ID = $request->query->get('RegistrationShirt_ID');
                $registrationShirt = $this->get('repository_registrationshirt')->getFromRegistrationShirtId($registrationShirt_ID);
                $shirt = $registrationShirt->getShirt();

                $entityManager->remove($registrationShirt);
                $entityManager->flush();

                $returnData['message'] = 'Deleted shirt size ' . $shirt->getShirttype() . ' ' . $shirt->getShirtsize() . ' from registration';
                $returnData['success'] = true;
                 */
            }
        }

        if ($registration->getRegistrationId() && $returnData['success']) {
            $registrationHistory = new History();
            $registrationHistory->setRegistration($registration);
            $registrationHistory->setChangetext($returnData['message']);
            $entityManager->persist($registrationHistory);

            $entityManager->flush();
        }

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/registration/list/extra/{registrationId}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @return JsonResponse
     */
    public function getRegistrationExtras($registrationId)
    {
        $arrayExtras = [];
        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        if ($registration) {
            /** @var Extra[] $extras */
            $extras = $registration->getExtras();

            foreach ($extras as $extra) {
                $tmp = array();
                $tmp['RegistrationExtraId'] = $extra->getExtraId();
                $tmp['extra'] = $extra->getName();
                $arrayExtras[] = $tmp;
            }
        }

        return new JsonResponse($arrayExtras);
    }

    /**
     * @Route("/registration/ajax/extra/{registrationId}/{action}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $registrationId
     * @param String $action
     * @return JsonResponse
     */
    public function modifyExtras(Request $request, $registrationId, $action)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $returnData = array();
        $returnData['success'] = false;
        $returnData['message'] = '';

        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        if (!$registration) {
            $returnData['message'] = 'Invalid Registration ID';

            return new JsonResponse($returnData);
        }

        if ($action == 'add') {
            if ($request->query->has('extra')) {
                $extraName = $request->query->get('extra');

                $extra = $this->getDoctrine()->getRepository(Extra::class)->getExtraFromName($extraName);
                if (!$extra) {
                    $returnData['message'] = 'Error: Invalid Extra';

                    return new JsonResponse($returnData);
                }

                /** @var Extra[] $extras */
                $extras = $registration->getExtras();
                foreach ($extras as $foundExtra) {
                    if ($foundExtra->getExtraId() == $extra->getExtraId()) {
                        $returnData['message'] = 'Extra already added to Registration!';

                        return new JsonResponse($returnData);
                    }
                }

                $registration->addExtra($extra);
                $entityManager->flush();

                $returnData['success'] = true;
                $returnData['message'] = "Added extra $extraName to registration";
                $returnData['RegistrationExtraId'] = $extra->getExtraId(); // FIXME: Wrong ID?
            }
        } elseif ($action == 'delete') {
            if ($request->query->has('RegistrationExtraId')) {
                // FIXME: Bad stuff below
                $registrationExtraId = $request->query->get('RegistrationExtraId');
                $registrationExtra = $this->get('repository_registrationextra')->getFromRegistrationExtraId($registrationExtraId);
                $extra = $registrationExtra->getExtra();

                $entityManager->remove($registrationExtra);
                $entityManager->flush();

                $returnData['message'] = "Deleted extra {$extra->getName()} from registration";
                $returnData['success'] = true;
            }
        }

        if ($registration->getRegistrationId() && $returnData['success']) {
            $registrationHistory = new History();
            $registrationHistory->setRegistration($registration);
            $registrationHistory->setChangetext($returnData['message']);
            $entityManager->persist($registrationHistory);

            $entityManager->flush();
        }

        return new JsonResponse($returnData);
    }

    /**
     * @Route("/registration/ajax/edit")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditRegistration(Request $request)
    {
        // FIXME: Fix this
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
        if (!$request->request->has('regtype') || !$request->request->get('regtype')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'Registration Type was not set.';
        }
        if (!$request->request->has('Birthday') || $request->request->get('Birthday') == '') {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthday was not set.';
        }
        if (!$request->request->has('Birthyear') || $request->request->get('Birthyear') == '') {
            $all_fields_sent = false;
            $returnJson['message'] = 'Birthyear was not set.';
        }
        if (!$request->request->has('RegistrationType') || !$request->request->get('RegistrationType')) {
            $all_fields_sent = false;
            $returnJson['message'] = 'RegistrationType was not set.';
        }
        if (!$request->request->has('RegistrationStatus') || !$request->request->get('RegistrationStatus')) {
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
                ->getFromRegistrationId($request->request->get('TransferredFrom'));
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
                    if ($registration->getRegistrationId()) {
                        $oldDate = $registration->getBirthday()->format('m/d/y');
                        if ($oldDate != $newDate->format('m/d/y')) {
                            $history .= "$field: $oldDate => {$newDate->format('m/d/y')}<br>";
                        }
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

            $registration->setContactVolunteer(false);
            if ($request->request->has('volunteer') && $request->request->get('volunteer')) {
                $registration->setContactVolunteer(true);
            }
            $registration->setContactNewsletter(false);
            if ($request->request->has('newsletter') && $request->request->get('newsletter')) {
                $registration->setContactNewsletter(true);
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
                if ($registration->getBirthday()->getTimestamp() > strtotime($event->getStartdate()->format('Y-m-d') . " -18 years")) {
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
                $registration->setTransferedto($transferredFrom);
                $url = $this->generateUrl('viewRegistration', ['registrationId' => $transferredFrom->getRegistrationId()]);
                $history .= " Transferred From <a href='$url'>" . $transferredFrom->getFirstname()
                    . ' ' . $transferredFrom->getLastname() . '</a>. <br>';
            }

            $entityManager->persist($registration);
            $entityManager->flush();

            if ($transferredFrom) {
                $transferredRegistrationStatus = $this->get('repository_registrationstatus')
                    ->getRegistrationStatusFromStatus('Transferred');
                $transferredFrom->setRegistrationstatus($transferredRegistrationStatus);
                $entityManager->persist($transferredFrom);

                $transferredFromHistory = '';
                $registrationRegGroups = $this->get('repository_registrationreggroup')
                    ->getRegistrationRegGroupFromRegistration($transferredFrom);
                foreach ($registrationRegGroups as $registrationRegGroup) {
                    $tmpRegGroup = $registrationRegGroup->getReggroup();
                    $transferredFromHistory .= "Group Removed: {$tmpRegGroup->getName()}<br>";
                    $entityManager->remove($registrationRegGroup);
                }

                $registrationHistory = new History();
                $registrationHistory->setRegistration($transferredFrom);
                $url = $this->generateUrl('viewRegistration', ['registrationId' => $registration->getRegistrationId()]);
                $transferredToText= "<br>Registration Transferred to "
                    ."<a href='$url'>"
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

            $registrationHistory = new History();
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
