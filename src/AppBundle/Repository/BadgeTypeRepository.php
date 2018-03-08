<?php

namespace AppBundle\Repository;


use AppBundle\Entity\BadgeType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class BadgeTypeRepository extends EntityRepository
{
    /**
     * @param $type
     * @return BadgeType
     */
    public function getBadgeTypeFromType($type)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('bt')
            ->from(BadgeType::class, 'bt')
            ->where("bt.name = :type")
            ->setParameter('type', $type);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}