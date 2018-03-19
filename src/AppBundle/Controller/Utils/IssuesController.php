<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Utils;

use AppBundle\Entity\Registration;
use AppBundle\Entity\History;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IssuesController extends Controller
{
    /**
     * @Route("/issues/", name="issuesList")
     * @Route("/issues/{curPageNum}", name="issuesList_WithPageNum")
     * @Route("/issues/{curPageNum}/", name="issuesList_WithPageNum_slash")
     * @Security("has_role('ROLE_SUBHEAD')")
     *
     * @param Request $request
     * @param int $curPageNum
     * @return Response
     */
    public function issueSearch(Request $request, $curPageNum = 1) {
        $vars = [];
        $registrationRepository = $this->getDoctrine()->getRepository(Registration::class);

        $limit = 90;
        if ($request->query->has('limit')
            && is_numeric($request->query->get('limit'))
            && (int) $request->query->get('limit') <= 90
            && (int) $request->query->get('limit') > 0
        ) {
            $limit = (int) $request->query->get('limit');
        }
        $vars['limit'] = $limit;

        $searchText = '';
        if ($request->query->has('searchText')) {
            $searchText = $request->query->get('searchText');
        };
        $vars['searchText'] = $searchText;

        $registrations = $registrationRepository->getRegistrationsWithRevokedBadges($searchText);
        $count = count($registrations);
        $vars['total'] = $count;

        $totalPages = ceil($count / $limit);
        $curPageNum = max(min($totalPages, $curPageNum), 1);
        $vars['curPageNum'] = (int) $curPageNum;
        $vars['totalPages'] = (int) $totalPages;

        $offset = $limit * ($curPageNum - 1);
        $vars['offset'] = (int) $offset;

        $vars['registrations'] = $registrations;

        return $this->render('utils/issues.html.twig', $vars);
    }
}
