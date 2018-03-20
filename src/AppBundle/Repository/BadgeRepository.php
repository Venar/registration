<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityRepository;

class BadgeRepository extends EntityRepository
{

    public function generateNumber($digits = 4)
    {
        $number = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return $number;
    }

    /**
     * @param Registration|null $registration
     * @return bool
     */
    public function isStaff($registration) {
        if (!$registration) {
            return false;
        }
        $badges = $registration->getBadges();
        foreach ($badges as $badge) {
            if ($badge->getBadgetype()->getName() == 'STAFF') {

                return true;
            }
        }

        return false;
    }
}