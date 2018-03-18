<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Backend\Template;

use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    public function menuRoutingAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $sections = [];
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $sections = [
                [
                    'name' => 'Groups',
                    'items' => [
                        [
                            'title' => 'Groups',
                            'url' => 'group_list',
                        ],
                    ]
                ],
                [
                    'name' => 'Manage Registrations',
                    'items' => [
                        [
                            'title' => 'Manage Registrations',
                            'url' => 'listRegistrations',
                        ],
                    ]
                ],
                [
                    'name' => 'Statistics',
                    'items' => [
                        [
                            'title' => 'Statistics',
                            'url' => 'statistics',
                        ],
                    ]
                ],
                [
                    'name' => 'Tools',
                    'items' => [
                        [
                            'title' => 'Registration Form',
                            'url' => 'form_registration',
                        ],
                        [
                            'title' => 'Transfer Form',
                            'url' => 'form_transfer',
                        ],
                        [
                            'title' => 'List Badges to Print',
                            'url' => 'printing_list',
                        ],
                        [
                            'title' => 'T-Shirt Orders',
                            'url' => 'shirt_list',
                        ],
                        [
                            'title' => 'Edit History',
                            'url' => 'edit_history',
                        ],
                        [
                            'title' => 'Error Finder',
                            'url' => 'error_finder',
                        ],
                    ]
                ],
            ];
        }

        return $this->render(':Backend/template:menu.sub.html.twig', array('sections' => $sections));
    }

    public function switchYearsAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $eventRepository = $this->getDoctrine()
                ->getRepository(Event::class);

            $years = [];
            $currentYear = $eventRepository->getSelectedEvent()->getYear();
            $events = $eventRepository->findAll();
            foreach ($events as $event) {
                $years[] = $event->getYear();
            }

            return $this->render('template/switch_year.sub.html.twig', array('selectedYear' => $currentYear, 'years' => $years));
        }

        return new Response();
    }
}