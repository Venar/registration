<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 1:56 PM
 */

namespace AppBundle\Service\Entity;


use AppBundle\Entity\Badgetype;
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

    public function getBadgeTypeFromType(String $type) : ?Badgetype
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('bt')
            ->from(self::entityName, 'bt')
            ->where("bt.name = :type")
            ->setParameter('type', $type);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Badgetype[]
     */
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}