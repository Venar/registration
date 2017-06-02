<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badgestatus;
use Doctrine\ORM\EntityManager;


class BadgeStatusRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Badgestatus';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $status
     * @return Badgestatus|null
     */
    public function getBadgeStatusFromStatus($status)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('bs')
            ->from(self::entityName, 'bs')
            ->where("bs.status = :status")
            ->setParameter('status', $status);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Badgestatus[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
