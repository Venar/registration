<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badgetype;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationhistory;
use Doctrine\ORM\EntityManager;

class RegistrationHistoryRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationhistory';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Registration $registration
     * @return Registrationhistory[]
     */
    public function getHistoryFromRegistration(Registration $registration)
    {
        $history = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $history;
    }

    /**
     * @param String $searchText
     * @param int $limit
     * @param int $offset
     * @return Registrationhistory[]
     */
    public function getHistoryFromSearch($searchText, $limit = null, $offset = null)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('rh')
            ->from('AppBundle:Registrationhistory', 'rh')
            ->innerJoin('rh.createdby', 'cb')
            ->innerJoin('rh.registration', 'r')
            ->where("rh.changetext LIKE :changetext")
            ->orWhere('cb.firstname LIKE :firstname')
            ->orWhere('cb.lastname LIKE :lastname')
            ->orWhere('r.registrationId = :registrationId')
            ->setParameter('registrationId', "$searchText")
            ->setParameter('changetext', "%$searchText%")
            ->setParameter('firstname', "%$searchText%")
            ->setParameter('lastname', "%$searchText%")
            ->orderBy('rh.createddate', 'DESC')
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

    /**
     * @return Registrationhistory[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}