<?php

namespace AppBundle\Controller\Legal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LegalController extends Controller
{
    /**
     * @Route("/privacy", name="privacy")
     */
    public function showLogin()
    {
        return $this->render('legal/privacy.html.twig');
    }
}
