<?php declare(strict_types=1);

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            /** @var $user User */
            return $this->render('user/currentuser.sub.html.twig', array('name' => $user->getUsername(), 'userid' => $user->getId()));
        }

        return new Response();
    }
}
