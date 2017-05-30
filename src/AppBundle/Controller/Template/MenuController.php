<?php declare(strict_types=1);

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
                        'url' => 'group/list',
                    ],
                ]
            ],
            [
                'name' => 'Manage Registrations',
                'items' => [
                    [
                        'title' => 'Manage Registrations',
                        'url' => 'manage',
                    ],
                ]
            ],
            [
                'name' => 'Statistics',
                'items' => [
                    [
                        'title' => 'Statistics',
                        'url' => 'stats',
                    ],
                ]
            ],
            [
                'name' => 'Tools',
                'items' => [
                    [
                        'title' => 'Registration Form',
                        'url' => 'registrationformyear',
                    ],
                    [
                        'title' => 'List Badges to Print',
                        'url' => 'print_list',
                    ],
                    [
                        'title' => 'Percentage Tools',
                        'url' => 'percentagetools',
                    ],
                    [
                        'title' => 'Edit History',
                        'url' => 'history',
                    ],
                    [
                        'title' => 'Error Finder',
                        'url' => 'errorfinder',
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
