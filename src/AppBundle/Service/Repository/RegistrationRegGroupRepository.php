<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Event;
use AppBundle\Entity\Reggroup;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class RegistrationRegGroupRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationreggroup';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $registrationRegGroupId
     * @return Registrationreggroup|null
     */
    public function getFromRegistrationreggroupId($registrationRegGroupId)
    {
        if (!$registrationRegGroupId) {

            return null;
        }

        $registrationRegGroup = $this->entityManager
            ->getRepository('AppBundle:Registrationreggroup')
            ->find($registrationRegGroupId)
        ;

        if (!$registrationRegGroup) {
            /*
            throw $this->createNotFoundException(
                'No registration group found for id '.$reggroupId
            );
            */
        }

        return $registrationRegGroup;
    }

    /**
     * @param Registration $registration
     * @return Registrationreggroup[]
     */
    public function getRegistrationRegGroupFromRegistration(Registration $registration)
    {
        if (!$registration) {

            return [];
        }

        $registrationRegGroup = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $registrationRegGroup;
    }

    /**
     * @param Reggroup $regGroup
     * @return Registrationreggroup[]
     */
    public function getRegistrationRegGroupFromRegGroup(Reggroup $regGroup)
    {
        if (!$regGroup) {

            return [];
        }

        $registrationRegGroup = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('reggroup' => $regGroup)
            );

        return $registrationRegGroup;
    }

    /**
     * @return Registrationreggroup[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}