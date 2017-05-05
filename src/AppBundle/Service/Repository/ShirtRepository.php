<?php declare(strict_types=1);

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
     * @param Registration $registration
     * @return Shirt[]
     */
    public function getShirtsFromRegistration(Registration $registration) : array
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
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}