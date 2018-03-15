<?php

namespace AppBundle\Controller\Backend\Template;

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

        return $this->render(':Backend/template:menu.sub.html.twig', array('sections' => $sections));
    }
}