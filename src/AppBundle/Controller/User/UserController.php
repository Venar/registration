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
    /**
     * @Route("/user/list")
     * @Route("/user/list/")
     * @Route("/user/list/{curPageNum}")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param String $curPageNum Current page number
     * @return Response
     */
    public function listUsers(Request $request, $curPageNum = '1')
    {
        $vars = [];

        $searchText = '';
        if ($request->query->has('searchText')) {
            $searchText = $request->query->get('searchText');
        }

        $users = $this->get('repository_user')->findAll();

        $vars['users'] = $users;

        $vars['totalResults'] = count($users);
        $vars['searchText'] = $searchText;

        $roles = $this->get('security.role_hierarchy');
        //var_dump($roles);

        return $this->render('user/list.html.twig', $vars);
    }

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
