<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\BadgeType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class BadgeTypeRepository extends EntityRepository
{
    /**
     * @param $type
     * @return BadgeType
     */
    public function getBadgeTypeFromType($type)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('bt')
            ->from(BadgeType::class, 'bt')
            ->where("bt.name = :type")
            ->setParameter('type', $type);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}