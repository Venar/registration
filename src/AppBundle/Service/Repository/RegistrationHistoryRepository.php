<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badgetype;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationhistory;
use Doctrine\ORM\EntityManager;

class RegistrationHistoryRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationhistory';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Registration $registration
     * @return Registrationhistory[]
     */
    public function getHistoryFromRegistration(Registration $registration) : array
    {
        $history = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $history;
    }

    /**
     * @return Registrationhistory[]
     */
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}