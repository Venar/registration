<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\RegistrationType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class RegistrationTypeRepository extends EntityRepository
{
    /**
     * MyEntity repository.
     *
     * @method RegistrationType[] findAll
     */

    /**
     * @param String $type
     * @return RegistrationType|null
     */
    public function getRegistrationTypeFromType($type)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('rt')
            ->from(RegistrationType::class, 'rt')
            ->where("rt.name = :type")
            ->setParameter('type', $type);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}