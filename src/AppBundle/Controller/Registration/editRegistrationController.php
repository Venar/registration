<?php declare(strict_types=1);

namespace AppBundle\Controller\Registration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return Response
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
}
