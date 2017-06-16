<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 1:56 PM
 */

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;

class BadgeRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Badge';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

        $badge = $this->entityManager
            ->getRepository('AppBundle:Badge')
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

        $badge = $this->entityManager
            ->getRepository(self::entityName)
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

    /**
     * @return Badge[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
