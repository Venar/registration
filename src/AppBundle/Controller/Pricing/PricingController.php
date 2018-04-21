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

        $parameters['badgeTypes'] = $this->getDoctrine()->getRepository(BadgeType::class)->findBy([], ['description' => 'ASC']);

        $parameters['pricing'] = [];
        $parameters['badgeTypeNames'] = [];
        $parameters['badgeTypeDescriptions'] = [];
        $parameters['displayBadgeTypes'] = [];
        $emptyBadgeTypes = [];
        foreach ($parameters['badgeTypes'] as $badgeType) {
            $parameters['badgeTypeNames'][] = $badgeType->getName();
            $parameters['badgeTypeDescriptions'][$badgeType->getName()] = [
                'color' => $badgeType->getColor(),
                'name' => $badgeType->getDescription(),
            ];
            $pricing = $this->getDoctrine()->getRepository(Pricing::class)->getPricingForBadgeType($badgeType);

            $pricingArray = [];
            $pricingKeys = [];
            foreach ($pricing as $price) {
                $tmp = [
                    'id' => $price->getId(),
                    'start' => $price->getPricingBegin()->format('U'),
                    'end' => $price->getPricingEnd()->format('U'),
                    'currency' => $price->getCurrency(),
                    'price' => $price->getPrice(),
                    'description' => $price->getDescription(),
                ];
                $pricingArray[$price->getPricingBegin()->format('U')] = $tmp;
                $pricingKeys[] = (int) $price->getPricingBegin()->format('U');
            }

            $tmp = [
                'pricing' => $pricingArray,
                'pricingKeys' => $pricingKeys,
            ];
            $parameters['pricing'][$badgeType->getName()] = $tmp;

            if (count($pricingArray) > 0) {
                $parameters['displayBadgeTypes'][] = $badgeType;
            } else {
                $emptyBadgeTypes[] = $badgeType;
            }
        }

        foreach ($emptyBadgeTypes as $badgeType) {
            $parameters['displayBadgeTypes'][] = $badgeType;
        }

        return $this->render('pricing/pricingEditor.html.twig', $parameters);
    }
}
