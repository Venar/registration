<?php

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
            $queryBuilder->where($queryBuilder->expr()->like('rg.school', ':school'))
                ->setParameter('school', "%$school%");
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