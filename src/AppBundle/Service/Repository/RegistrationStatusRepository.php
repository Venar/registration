<?php declare(strict_types=1);

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

    public function getRegistrationStatusFromStatus(String $status) : ?Registrationstatus
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
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
