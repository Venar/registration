<?php

namespace AppBundle\Controller\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    public function menuRoutingAction()
    {
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

        return $this->render('template/menu.sub.html.twig', array('sections' => $sections));
    }

    public function switchYearsAction()
    {
        $years = [];

        $currentYear = $this->get('repository_event')->getSelectedEvent()->getYear();
        $events = $this->get('repository_event')->findAll();
        foreach ($events as $event) {
            $years[] = $event->getYear();
        }

        return $this->render('template/switch_year.sub.html.twig', array('selectedYear' => $currentYear, 'years' => $years));
    }
}
