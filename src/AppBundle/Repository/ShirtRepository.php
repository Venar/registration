<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Registration;
use AppBundle\Entity\Shirt;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ShirtRepository extends EntityRepository
{
    /**
     * @param String $type
     * @param String $size
     * @return Shirt|null
     */
    public function getShirtFromTypeAndSize($type, $size)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('s')
            ->from('AppBundle:Shirt', 's')
            ->where("s.size = :size")
            ->andWhere("s.type = :type")
            ->setParameter('size', $size)
            ->setParameter('type', $type);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param Registration $registration
     * @return Shirt[]
     */
    public function getShirtsFromRegistration(Registration $registration)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('s')
            ->from(Shirt::class, 's')
            ->innerJoin(Registration::class, 'r')
            ->where("r.id = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}