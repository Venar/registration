<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Group;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class RegGroupRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Group';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $reggroupId
     * @return Group|null
     */
    public function getFromReggroupId($reggroupId)
    {
        if (!$reggroupId) {

            return null;
        }

        $regGroup = $this->entityManager
            ->getRepository('Group.php')
            ->find($reggroupId)
        ;

        if (!$regGroup) {
            /*
            throw $this->createNotFoundException(
                'No registration group found for id '.$reggroupId
            );
            */
        }

        return $regGroup;
    }

    /**
     * @param String $school
     * @return Group[]
     */
    public function findFromSchool($school = '')
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('rg')
            ->from('Group.php', 'rg')
            ->orderBy('rg.name', 'ASC');

        if ($school != '') {
            $queryBuilder->where($queryBuilder->expr()->like('rg.school', ':school'))
                ->setParameter('school', "%$school%");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Registration $registration
     * @return Group
     */
    public function getRegGroupFromRegistration(Registration $registration)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('rg')
            ->from('Group.php', 'rg')
            ->innerJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH, 'rrg.reggroup = rg.reggroupId')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = rrg.registration')
            ->where("r.registrationId = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Group[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}