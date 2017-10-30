<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 11:13 AM
 */

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetPercentController extends Controller
{
    /**
     * @Route("/api/percent", name="api_percent")
     * @Route("/api/percent/", name="api_percent")
     */
    public function numberAction()
    {
        $number = $this->get('util_percent')->getPercent();

        // $number = mt_rand(0, 100);

        $jsonArray = [
            'percent' => $number,
        ];

        return new JsonResponse($jsonArray);
    }
}