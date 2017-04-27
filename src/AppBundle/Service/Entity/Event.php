<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 11:13 AM
 */

namespace AppBundle\Service\Entity;

use AppBundle\Entity\Event as EntityEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class Event
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Event';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCurrentEvent() : ?EntityEvent
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
            ->from(self::entityName, 'e')
            ->where("e.active = :active")
            ->setParameter('active', 'true');

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


    public function getEventFromYear(String $year) : ?EntityEvent
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
            ->from(self::entityName, 'e')
            ->where("e.year = :year")
            ->setParameter('year', $year);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getSelectedEvent() : ?EntityEvent
    {
        $session = new Session();
        $selectedEvent = $session->get('selectedEvent');
        if (!$selectedEvent) {

            return $this->getCurrentEvent();
        }

        return $this->getEventFromYear($selectedEvent);
    }
}