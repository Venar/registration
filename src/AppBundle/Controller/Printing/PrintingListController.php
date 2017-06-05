<?php

namespace AppBundle\Controller\Printing;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class PrintingListController extends Controller
{
    /**
     * @Route("/printing/list")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function printingList() {

        return $this->render('printing/list.html.twig');
    }
}
