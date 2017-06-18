<?php

namespace AppBundle\Controller\Testing;

use AppBundle\Entity\Extra;
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
     * @Route("/test/migration")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function testEmail()
    {
        $extra = $this->get('repository_extra')->getExtraFromName('SponsorBreakfast');
        if (!$extra) {
            $extra = new Extra();
            $extra->setName('SponsorBreakfast');
            $extra->setDescription('Has purchased the Sunday sponsor breakfast.');
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->persist($extra);
            $entityManager->flush();
        }

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
