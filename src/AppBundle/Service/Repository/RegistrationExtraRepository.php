<?php
/**
 * Created by PhpStorm.
 * User: jjkoniges
 * Date: 6/18/17
 * Time: 9:37 AM
 */

namespace AppBundle\Service\Repository;

use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationextra;
use Doctrine\ORM\EntityManager;


class RegistrationExtraRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationextra';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $registrationExtraId
     * @return Registrationextra|null|object
     */
    public function getFromRegistrationExtraId($registrationExtraId)
    {
        if (!$registrationExtraId) {

            return null;
        }

        $registration = $this->entityManager
            ->getRepository(self::entityName)
            ->find($registrationExtraId)
        ;

        return $registration;
    }

    /**
     * @param Registration $registration
     * @return Registrationextra[]
     */
    public function getRegistrationExtrasFromRegistration(Registration $registration)
    {
        $registrationExtras = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $registrationExtras;
    }
    /**
     * @return Registrationextra[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
