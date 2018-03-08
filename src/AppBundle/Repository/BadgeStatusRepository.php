<?php

namespace AppBundle\Repository;


use AppBundle\Entity\BadgeStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class BadgeStatusRepository extends EntityRepository
{
    /**
     * @param String $status
     * @return BadgeStatus|null
     */
    public function getBadgeStatusFromStatus($status)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('bs')
            ->from(BadgeStatus::class, 'bs')
            ->where("bs.status = :status")
            ->setParameter('status', $status);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}