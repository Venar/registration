<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

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

    /**
     * @return RegistrationStatus[]
     */
    public function findAllActive()
    {
        $criteria = [
            'active' => true,
        ];
        return $this->findBy($criteria);
    }

    /**
     * @return RegistrationStatus[]
     */
    public function findAllInactive()
    {
        $criteria = [
            'active' => false,
        ];
        return $this->findBy($criteria);
    }
}