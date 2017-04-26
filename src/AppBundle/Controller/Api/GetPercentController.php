<?php


namespace AppBundle\Controller\Api;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class GetPercentController
{
    /**
     * @Route("/api/percent")
     */
    public function numberAction()
    {
        $number = mt_rand(0, 100);

        $jsonArray = [
            'percent' => $number,
        ];

        $event = new Event();


        return new Response(
            json_encode($jsonArray)
        );
    }
}