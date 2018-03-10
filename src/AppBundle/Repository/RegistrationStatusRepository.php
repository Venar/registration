<?php

namespace AppBundle\Repository;


use AppBundle\Entity\RegistrationStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class RegistrationStatusRepository extends EntityRepository
{
    /**
     * @param String $status
     * @return RegistrationStatus|null
     */
    public function getRegistrationStatusFromStatus($status)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('rs')
            ->from(RegistrationStatus::class, 'rs')
            ->where("rs.status = :status")
            ->setParameter('status', $status);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}