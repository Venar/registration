<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/29/2017
 */

namespace AppBundle\Service\Repository;


use AppBundle\Entity\RegistrationType;
use Doctrine\ORM\EntityManager;

class RegistrationTypeRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Registrationtype';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $type
     * @return RegistrationType|null
     */
    public function getRegistrationTypeFromType($type)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('rt')
            ->from(self::entityName, 'rt')
            ->where("rt.name = :type")
            ->setParameter('type', $type);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return RegistrationType[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}