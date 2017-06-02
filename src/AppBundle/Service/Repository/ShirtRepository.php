<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Registration;
use AppBundle\Entity\Shirt;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class ShirtRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Shirt';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $type
     * @param String $size
     * @return Shirt|null
     */
    public function getShirtFromTypeAndSize($type, $size)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('s')
            ->from('AppBundle:Shirt', 's')
            ->where("s.shirtsize = :shirtsize")
            ->andWhere("s.shirttype = :shirttype")
            ->setParameter('shirtsize', $size)
            ->setParameter('shirttype', $type);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Registration $registration
     * @return Shirt[]
     */
    public function getShirtsFromRegistration(Registration $registration)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('s')
            ->from(self::entityName, 's')
            ->innerJoin('AppBundle\Entity\Registrationshirt', 'rs', Join::WITH, 'rs.shirt = s.shirtId')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = rs.registration')
            ->where("r.registrationId = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @return Shirt[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}