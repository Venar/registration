<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Pricing;

use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\Pricing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PricingController extends Controller
{
    /**
     * @Route("/pricing", name="pricing")
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function pricingEditor()
    {
        $parameters = [];

        $parameters['event'] = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        $parameters['badgeTypes'] = $this->getDoctrine()->getRepository(BadgeType::class)->findAll();

        $parameters['pricing'] = [];
        $parameters['badgeTypeNames'] = [];
        $parameters['badgeTypeDescriptions'] = [];
        foreach ($parameters['badgeTypes'] as $badgeType) {
            $parameters['badgeTypeNames'][] = $badgeType->getName();
            $parameters['badgeTypeDescriptions'][$badgeType->getName()] = $badgeType->getDescription();
            $pricing = $this->getDoctrine()->getRepository(Pricing::class)->getPricingForBadgeType($badgeType);

            $pricingArray = [];
            foreach ($pricing as $price) {
                $tmp = [
                    'id' => $price->getId(),
                    'start' => $price->getPricingBegin()->format('U'),
                    'end' => $price->getPricingEnd()->format('U'),
                    'currency' => $price->getCurrency(),
                    'price' => $price->getPrice(),
                    'description' => $price->getDescription(),
                ];
                $pricingArray[] = $tmp;
            }

            $tmp = [
                'pricing' => $pricingArray,
            ];
            $parameters['pricing'][$badgeType->getName()] = $tmp;
        }

        return $this->render('pricing/pricingEditor.html.twig', $parameters);
    }
}
