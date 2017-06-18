<?php

namespace AppBundle\Controller\Testing;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TestingController extends Controller
{
    /**
     * @Route("/testing")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function test()
    {
        $test = $this->getParameter('postback.sha1_key.asecurecart');
        var_dump($test);
        die();
    }

    /**
     * @Route("/test/email")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function testEmail()
    {
        $registration = $this->get('repository_registration')->getFromRegistrationId(5488);
        $badges = $this->get('repository_badge')->getBadgesFromRegistration($registration);
        $this->get('repository_registration')->sendConfirmationEmail($registration, $badges, true);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
