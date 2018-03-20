<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Printing;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class PrintingListController extends Controller
{
    /**
     * @Route("/printing/list", name="printing_list")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function printingList() {

        return $this->render('printing/list.html.twig');
    }
}
