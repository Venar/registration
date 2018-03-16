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
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if ($user instanceof User) {
                /** @var $user User */
                $vars = [
                    'name' => "{$user->getFirstName()} {$user->getLastname()}",
                    'userid' => $user->getId(),
                ];

                return $this->render('user/currentuser.sub.html.twig', $vars);
            }
        }

        return new Response();
    }
}
