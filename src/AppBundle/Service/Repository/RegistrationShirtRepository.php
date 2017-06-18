<?php

namespace AppBundle\Service\Repository;

use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationshirt;
use Doctrine\ORM\EntityManager;


class RegistrationShirtRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationshirt';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $registrationShirtId
     * @return RegistrationShirt|null|object
     */
    public function getFromRegistrationShirtId($registrationShirtId)
    {
        if (!$registrationShirtId) {

            return null;
        }

        $registration = $this->entityManager
            ->getRepository('AppBundle:Registrationshirt')
            ->find($registrationShirtId)
            //->getQuery()->getOneOrNullResult()
        ;

        if (!$registration) {
            /*
            throw $this->createNotFoundException(
                'No registration found for id '.$registrationId
            );
            */
        }

        return $registration;
    }

    /**
     * @param Registration $registration
     * @return Registrationshirt[]
     */
    public function getRegistrationShirtsFromRegistration(Registration $registration)
    {
        $registrationShirts = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $registrationShirts;
    }
    /**
     * @return Registrationshirt[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
