<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Group;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class GroupRepository extends EntityRepository
{
    /**
     * @param String $school
     * @return Group[]
     */
    public function findFromSchool($school = '')
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.name', 'ASC');

        if ($school != '') {
            $queryBuilder
                ->where($queryBuilder->expr()->like('g.school', ':school'))
                ->orWhere($queryBuilder->expr()->like('g.name', ':name'))
                ->setParameter('school', "%$school%")
                ->setParameter('name', "%$school%");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Registration $registration
     * @return Group
     */
    public function getGroupFromRegistration(Registration $registration)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('g')
            ->from(Group::class, 'g')
            ->innerJoin(Registration::class, 'r')
            ->where("r.id = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}