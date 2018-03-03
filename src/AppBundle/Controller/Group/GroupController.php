<?php

namespace AppBundle\Controller\Group;

use AppBundle\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GroupController extends Controller
{
    /**
     * @Route("/group/list", name="group_list")
     * @Route("/group/list/", name="group_list_slash")
     * @Route("/group/list/{curPageNum}", name="group_list_withPageNum")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $curPageNum Current page number
     * @return Response
     */
    public function listGroups(Request $request, $curPageNum = '1')
    {
        $vars = [];
        $event = $this->get('repository_event')->getSelectedEvent();

        $showEmptyGroups = false;
        if (
            $request->query->has('showEmptyGroups')
            && $request->query->get('showEmptyGroups') == '1'
        ) {
            $showEmptyGroups = true;
        }

        $searchText = '';
        if ($request->query->has('searchText')) {
            $searchText = $request->query->get('searchText');
        }

        $groups = $this->get('repository_reggroup')->findFromSchool($searchText);

        $vars['regGroups'] = [];
        foreach ($groups as $group) {
            $registrations = $this
                ->get('repository_registration')
                ->getRegistrationsFromRegGroup($group, $event);
            $count = count($registrations);

            if (!$showEmptyGroups && $count == 0) {
                continue;
            }

            $vars['regGroups'][] = [
                'regGroup' => $group,
                'count' => $count,
            ];
        }

        $vars['showEmptyChecked'] = $showEmptyGroups;
        $vars['totalResults'] = count($groups);
        $vars['searchText'] = $searchText;

        return $this->render('group/list.html.twig', $vars);
    }

    /**
     * @Route("/group/edit")
     * @Route("/group/edit/")
     * @Route("/group/edit/{regGroupId}")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $regGroupId
     * @return Response
     */
    public function groupEdit(Request $request, $regGroupId = null)
    {
        $vars = [];
        $vars['saveSuccessful'] = false;
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $event = $this->get('repository_event')->getSelectedEvent();

        $regGroup = $this->get('repository_reggroup')->getFromReggroupId($regGroupId);

        $vars['errors'] = [];
        if ($request->request->has('action') && $request->request->get('action') == 'save') {
            if (!$regGroup) {
                $regGroup = new Group();
            }

            $regGroup->setName($request->request->get('name'));
            $regGroup->setSchool($request->request->get('school'));
            $regGroup->setAddress($request->request->get('address'));
            $regGroup->setCity($request->request->get('city'));
            $regGroup->setState($request->request->get('state'));
            $regGroup->setZip($request->request->get('zip'));
            $regGroup->setLeader($request->request->get('leader'));
            $regGroup->setLeaderemail($request->request->get('leaderemail'));
            $regGroup->setLeaderphone($request->request->get('leaderphone'));
            $regGroup->setAuthorizedname($request->request->get('authorizedname'));
            $regGroup->setAuthorizedemail($request->request->get('authorizedemail'));
            $regGroup->setAuthorizedphone($request->request->get('authorizedphone'));

            if ($regGroup->getName() == '') {
                $vars['errors'][] = 'Name can not be blank!';
            }

            if (count($vars['errors']) == 0) {
                $entityManager->persist($regGroup);
                $entityManager->flush();
                $vars['saveSuccessful'] = true;
            }
        }

        $registrations = $this->get('repository_registration')->getRegistrationsFromRegGroup($regGroup, $event);

        $vars['regGroup'] = $regGroup;
        $vars['registrations'] = $registrations;

        return $this->render('group/edit.html.twig', $vars);
    }
}
