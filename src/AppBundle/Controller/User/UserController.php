<?php declare(strict_types=1);

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('user/currentuser.sub.html.twig', array('name' => 'John', 'userid' => 1));
    }
}
