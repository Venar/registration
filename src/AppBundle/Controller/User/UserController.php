<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    public function currentUserAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            /** @var $user User */
            return $this->render('user/currentuser.sub.html.twig', array('name' => $user->getUsername(), 'userid' => $user->getId()));
        }

        return new Response();
    }
}
