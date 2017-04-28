<?php declare(strict_types=1);

namespace AppBundle\Service\Entity;


use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use \AppBundle\Entity\Event;

class RegistrationRepository
{
    /** @var EventRepository $event */
    protected $eventRepository;
    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(EventRepository $event, EntityManager $entityManager)
    {
        $this->eventRepository = $event;
        $this->entityManager = $entityManager;
    }

    public function getFromConfirmation(String $confirmation, Event $event) : ?Registration
    {
        if (!$event) {
            $event = $this->eventRepository->getCurrentEvent();
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from('AppBundle:Registration', 'r')
            ->where("r.confirmationnumber = :confirmationnumber")
            ->andWhere('r.event = :event')
            ->setParameter('confirmationnumber', $confirmation)
            ->setParameter('event', $event->getEventId());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}