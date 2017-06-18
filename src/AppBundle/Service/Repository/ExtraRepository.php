<?php
/**
 * Created by PhpStorm.
 * User: jjkoniges
 * Date: 6/18/17
 * Time: 9:33 AM
 */

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Registration;
use AppBundle\Entity\Extra;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class ExtraRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Extra';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $name
     * @return Extra|null
     */
    public function getExtraFromName($name)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ex')
            ->from('AppBundle:Extra', 'ex')
            ->where("ex.name = :name")
            ->setParameter('name', $name);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Registration $registration
     * @return Extra[]
     */
    public function getExtrasFromRegistration(Registration $registration)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ex')
            ->from(self::entityName, 'ex')
            ->innerJoin('AppBundle\Entity\Registrationextra', 're', Join::WITH, 're.extra = ex.extraId')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = re.registration')
            ->where("r.registrationId = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @return Extra[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
