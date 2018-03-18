<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Group;

use AppBundle\Entity\Event;
use AppBundle\Entity\Group;
use AppBundle\Entity\Registration;
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
     * @return Response
     */
    public function listGroups(Request $request)
    {
        $vars = [];
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        $showEmptyGroups = false;
        if (
            $request->query->has('showEmptyGroups')
            && $request->query->get('showEmptyGroups')
        ) {
            $showEmptyGroups = true;
        }

        $searchText = '';
        if ($request->query->has('searchText')) {
            $searchText = $request->query->get('searchText');
        }

        $groups = $this->getDoctrine()->getRepository(Group::class)->findFromSchool($searchText);

        $vars['groups'] = [];
        foreach ($groups as $group) {
            $registrations = $this
                ->getDoctrine()
                ->getRepository(Registration::class)
                ->getRegistrationsFromGroup($group, $event);
            $count = count($registrations);

            if (!$showEmptyGroups && $count == 0) {
                continue;
            }

            $vars['groups'][] = [
                'group' => $group,
                'count' => $count,
            ];
        }

        $vars['showEmptyChecked'] = $showEmptyGroups;
        $vars['totalResults'] = count($groups);
        $vars['searchText'] = $searchText;

        return $this->render('group/list.html.twig', $vars);
    }

    /**
     * @Route("/group/edit", name="groupEditNew")
     * @Route("/group/edit/", name="groupEditNewSlash")
     * @Route("/group/edit/{groupId}", name="groupEditExisting")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @param String $groupId
     * @return Response
     */
    public function groupEdit(Request $request, $groupId = null)
    {
        $vars = [];
        $vars['saveSuccessful'] = false;
        $entityManager = $this->getDoctrine()->getManager();
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        $group = null;
        if ($groupId) {
            $group = $this->getDoctrine()->getRepository(Group::class)->find($groupId);
        }

        $vars['errors'] = [];
        if ($request->request->has('action') && $request->request->get('action') == 'save') {
            if (!$group) {
                $group = new Group();
            }

            $group->setName($request->request->get('name'));
            $group->setSchool($request->request->get('school'));
            $group->setAddress($request->request->get('address'));
            $group->setCity($request->request->get('city'));
            $group->setState($request->request->get('state'));
            $group->setZip($request->request->get('zip'));
            $group->setLeader($request->request->get('leader'));
            $group->setLeaderemail($request->request->get('leaderemail'));
            $group->setLeaderphone($request->request->get('leaderphone'));
            $group->setAuthorizedname($request->request->get('authorizedname'));
            $group->setAuthorizedemail($request->request->get('authorizedemail'));
            $group->setAuthorizedphone($request->request->get('authorizedphone'));

            if ($group->getName() == '') {
                $vars['errors'][] = 'Name can not be blank!';
            }

            if (count($vars['errors']) == 0) {
                $entityManager->persist($group);
                $entityManager->flush();
                $vars['saveSuccessful'] = true;
            }
        }

        $registrations = $this
            ->getDoctrine()
            ->getRepository(Registration::class)
            ->getRegistrationsFromGroup($group, $event);

        $vars['group'] = $group;
        $vars['registrations'] = $registrations;

        return $this->render('group/edit.html.twig', $vars);
    }
}
