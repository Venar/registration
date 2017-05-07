<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;


class UserRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:User';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $userId
     * @return User|null
     */
    public function getFromUserId($userId)
    {
        if (!$userId) {

            return null;
        }

        $user = $this->entityManager
            ->getRepository('AppBundle:User')
            ->find($userId)
            //->getQuery()->getOneOrNullResult()
        ;

        if (!$user) {
            /*
            throw $this->createNotFoundException(
                'No registration found for id '.$registrationId
            );
            */
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}
