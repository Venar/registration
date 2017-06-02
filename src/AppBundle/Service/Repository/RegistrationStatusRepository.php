<?php

namespace AppBundle\Service\Repository;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Registrationstatus;

class RegistrationStatusRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationstatus';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $status
     * @return Registrationstatus|null
     */
    public function getRegistrationStatusFromStatus($status)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('rs')
            ->from(self::entityName, 'rs')
            ->where("rs.status = :status")
            ->setParameter('status', $status);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Registrationstatus[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
