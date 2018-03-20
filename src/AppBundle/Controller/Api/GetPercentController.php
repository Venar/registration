<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
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