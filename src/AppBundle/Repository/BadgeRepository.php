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
     * @param String $badgeId
     * @return Badge|null
     */
    public function getFromBadgeId($badgeId)
    {
        if (!$badgeId) {

            return null;
        }

        $badge = $this->getEntityManager()
            ->getRepository(Badge::class)
            ->find($badgeId)
        ;
        return $badge;
    }

    /**
     * @param null|Registration $registration
     * @return Badge[]
     */
    public function getBadgesFromRegistration(?Registration $registration)
    {
        if (!$registration) {

            return [];
        }

        $badge = $this->getEntityManager()
            ->getRepository(Badge::class)
            ->findBy(
                array('registration' => $registration)
            );

        return $badge;
    }

    /**
     * @param Registration|null $registration
     * @return bool
     */
    public function isStaff($registration) {
        $badges = $this->getBadgesFromRegistration($registration);
        foreach ($badges as $badge) {
            if ($badge->getBadgetype()->getName() == 'STAFF') {

                return true;
            }
        }

        return false;
    }
}