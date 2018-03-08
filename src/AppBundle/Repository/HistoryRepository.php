<?php

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

        $queryBuilder->select('rh')
            ->from('History', 'h')
            ->innerJoin('h.createdby', 'cb')
            ->innerJoin('h.registration', 'r')
            ->where("h.changetext LIKE :changetext")
            ->orWhere('cb.firstname LIKE :firstname')
            ->orWhere('cb.lastname LIKE :lastname')
            ->orWhere('r.registrationId = :registrationId')
            ->setParameter('registrationId', "$searchText")
            ->setParameter('changetext', "%$searchText%")
            ->setParameter('firstname', "%$searchText%")
            ->setParameter('lastname', "%$searchText%")
            ->orderBy('h.createddate', 'DESC')
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