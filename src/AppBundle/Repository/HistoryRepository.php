<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\History;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityRepository;

class HistoryRepository extends EntityRepository
{
    /**
     * @param Registration $registration
     * @return History[]
     */
    public function getHistoryFromRegistration(Registration $registration)
    {
        $history = $this->getEntityManager()
            ->getRepository(History::class)
            ->findBy(
                array('registration' => $registration)
            );

        return $history;
    }

    /**
     * @param String $searchText
     * @param int $limit
     * @param int $offset
     * @return History[]
     */
    public function getHistoryFromSearch($searchText, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('h')
            ->from(History::class, 'h')
            ->innerJoin('h.createdBy', 'cb')
            ->innerJoin('h.registration', 'r')
            ->where("h.changeText LIKE :changeText")
            ->orWhere('cb.firstName LIKE :firstName')
            ->orWhere('cb.lastName LIKE :lastName')
            ->orWhere('r.id = :registrationId')
            ->setParameter('registrationId', "$searchText")
            ->setParameter('changeText', "%$searchText%")
            ->setParameter('firstName', "%$searchText%")
            ->setParameter('lastName', "%$searchText%")
            ->orderBy('h.createdDate', 'DESC')
        ;

        $query = $queryBuilder->getQuery();
        if ($limit) {
            $query = $query->setMaxResults($limit);
        }
        if ($offset) {
            $query = $query->setFirstResult($offset);
        }
        return $query->getResult();
    }
}