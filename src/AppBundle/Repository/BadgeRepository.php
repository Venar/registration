<?php

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
        $badges = $registration->getBadges();
        foreach ($badges as $badge) {
            if ($badge->getBadgetype()->getName() == 'STAFF') {

                return true;
            }
        }

        return false;
    }
}