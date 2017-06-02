<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 4/27/2017
 * Time: 11:13 AM
 */

namespace AppBundle\Service\Repository;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class EventRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Event';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Event|null
     */
    public function getCurrentEvent()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
            ->from(self::entityName, 'e')
            ->where("e.active = :active")
            ->setParameter('active', true);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return String
     */
    public function getCurrentEventYear()
    {
        return $this->getCurrentEvent()->getYear();
    }

    /**
     * @param String $year
     * @return Event|null
     */
    public function getEventFromYear($year)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e')
            ->from(self::entityName, 'e')
            ->where("e.year = :year")
            ->setParameter('year', $year);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Event|null
     */
    public function getSelectedEvent()
    {
        $session = new Session();
        $selectedEvent = $session->get('selectedEvent');
        if (!$selectedEvent) {

            return $this->getCurrentEvent();
        }

        return $this->getEventFromYear($selectedEvent);
    }

    /**
     * @return Event[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(self::entityName)->findBy([], ['year' => 'DESC']);
    }
}
