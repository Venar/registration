<?php declare(strict_types=1);

namespace AppBundle\Controller\Login;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @Route("/login")
     */
    public function showLogin()
    {
        return $this->render('login/login.html.twig');
    }
}
