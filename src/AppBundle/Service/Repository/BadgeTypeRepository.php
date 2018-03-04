<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 1:56 PM
 */

namespace AppBundle\Service\Repository;


use AppBundle\Entity\BadgeType;
use Doctrine\ORM\EntityManager;

class BadgeTypeRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Badgetype';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $type
     * @return BadgeType
     */
    public function getBadgeTypeFromType($type)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('bt')
            ->from(self::entityName, 'bt')
            ->where("bt.name = :type")
            ->setParameter('type', $type);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return BadgeType[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(BadgeType::class)->findAll();
    }
}