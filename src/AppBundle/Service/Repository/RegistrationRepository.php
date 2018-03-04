<?php

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Group;
use AppBundle\Entity\Registration;
use AppBundle\Entity\RegistrationStatus;
use AppBundle\Entity\RegistrationType;
use AppBundle\Service\Util\Email;
use Doctrine\ORM\EntityManager;
use \AppBundle\Entity\Event;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\DependencyInjection\Container;

class RegistrationRepository
{
    /** @var EventRepository $event */
    protected $eventRepository;
    /** @var BadgeTypeRepository $event */
    protected $badgeTypeRepository;
    /** @var EntityManager $entityManager */
    protected $entityManager;
    /** @var Container $container */
    protected $container;
    /** @var Email $email */
    protected $email;

    public function __construct(EventRepository $event, BadgeTypeRepository $badgeTypeRepository,
        EntityManager $entityManager, Container $container, Email $email)
    {
        $this->eventRepository = $event;
        $this->badgeTypeRepository = $badgeTypeRepository;
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->email = $email;
    }

    /**
     * @param Registration $registration
     */
    private function generateConfirmationNumber(Registration $registration)
    {
        if ($registration->getConfirmationnumber()) {

            return; // Already have a number, so we return.
        }
        $unique = substr(md5(uniqid(rand(), true)), 16, 16);
        $confirmationNumber = substr($unique, 0, 8) . substr($registration->getNumber(), 1, 2)
            . substr($unique, 8, 2) . substr($registration->getNumber(), 3, 2)
            . substr($unique, 10, 2);
        $registration->setConfirmationnumber($confirmationNumber);
        $this->entityManager->persist($registration);
        $this->entityManager->flush();

        return;
    }

    /**
     * @param Registration $registration
     * @param Badge[] $badges
     * @param bool $forceResend Resend even if already set
     */
    public function sendConfirmationEmail(Registration $registration, array $badges, bool $forceResend = false)
    {
        if ($registration->getEmail() == ''
            || ($registration->getConfirmationnumber() != ''
                && !$forceResend
            )
        ) {

            return;
        }

        if ($registration->getConfirmationnumber() == '') {
            $this->generateConfirmationNumber($registration);
        }

        try {
            $this->email->sendConfirmationEmail($registration);
        } catch (\Exception $e) {
            $this->email->sendErrorMessageToRegistration($e->getMessage(), $registration);
        }
    }

    public function generateNumber(Registration $registration)
    {
        $event = $registration->getEvent();
        if (!$event) {
            $event = $this->eventRepository->getSelectedEvent();
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $count = $queryBuilder->select('count(r.registrationId)')
            ->from(Registration::class, 'r')
            ->where('r.event = :event')
            ->setParameter('event', $event)
            ->getQuery()->getSingleScalarResult();

        // Get count from only row returned.
        $number = ucwords(substr($registration->getLastname(), 0, 1))
            . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return $number;
    }

    /**
     * @param String $registrationId
     * @return Registration|null|object
     */
    public function getFromRegistrationId($registrationId)
    {
        if (!$registrationId) {

            return null;
        }

        $registration = $this->entityManager
            ->getRepository(Registration::class)
            ->find($registrationId)
            //->getQuery()->getOneOrNullResult()
        ;

        if (!$registration) {
            /*
            throw $this->createNotFoundException(
                'No registration found for id '.$registrationId
            );
            */
        }

        return $registration;
    }

    public function getFromTransfered($registrationId)
    {
        $registration = $this->entityManager
            ->getRepository(Registration::class)
            ->find($registrationId)
            //->getQuery()->getOneOrNullResult()
        ;

        if (!$registration) {
            /*
            throw $this->createNotFoundException(
                'No registration found for id '.$registrationId
            );
            */
        }

        return $registration;
    }

    /**
     * @param String $confirmation
     * @param Event $event
     * @return Registration|null
     */
    public function getFromConfirmation($confirmation, Event $event)
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

    /**
     * @param Group $regGroup
     * @param Event $event
     * @return Registration[]
     */
    public function getRegistrationsFromRegGroup(Group $regGroup = null, Event $event = null)
    {
        if (!$event) {
            $event = $this->eventRepository->getSelectedEvent();
        }
        if (!$regGroup) {
            return [];
        }
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(Registration::class, 'r')
            ->innerJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH, 'rrg.registration = r.registrationId')
            ->innerJoin(Group::class, 'rg', Join::WITH, 'rg.reggroupId = rrg.reggroup')
            ->where("rg.reggroupId = :reggroupId")
            ->andWhere("r.event = :event")
            ->setParameter('reggroupId', $regGroup)
            ->setParameter('event', $event)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param bool $showStaff
     * @param Event|null $event
     * @return Registration[]
     */
    public function findRegistrationsWithShirts($showStaff = false, Event $event = null) : array
    {
        if (!$event) {
            $event = $this->eventRepository->getSelectedEvent();
        }
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(Registration::class, 'r')
            ->innerJoin('AppBundle\Entity\Registrationshirt', 'rs', Join::WITH, 'rs.registration = r.registrationId')
            ->where("rs.registrationshirtId IS NOT NULL")
            ->andWhere("r.event = :event")
            ->setParameter('event', $event)
        ;

        if (!$showStaff) {
            $staffBadge = $this->badgeTypeRepository->getBadgeTypeFromType('STAFF');
            $allStaffBadges = $this->entityManager->createQueryBuilder()
                ->select('IDENTITY(b.registration)')
                ->from('AppBundle:Badge', 'b')
                ->where("b.badgetype = :stafftype")
                ->getDQL();

            $queryBuilder
                ->andWhere($queryBuilder->expr()->notIn('r.registrationId', $allStaffBadges))
                ->setParameter('stafftype', $staffBadge->getBadgetypeId());
        }

        return $queryBuilder->getQuery()->getResult();
    }
}