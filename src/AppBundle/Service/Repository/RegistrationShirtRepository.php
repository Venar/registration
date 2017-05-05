<?php declare(strict_types=1);

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
     * @param Registration $registration
     * @return Registrationshirt[]
     */
    public function getRegistrationShirtsFromRegistration(Registration $registration) : array
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
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}