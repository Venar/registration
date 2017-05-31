<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Badgetype;
use AppBundle\Entity\Reggroup;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registrationstatus;
use AppBundle\Entity\Registrationtype;
use AppBundle\Service\Util\Email;
use Doctrine\ORM\EntityManager;
use \AppBundle\Entity\Event;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\DependencyInjection\Container;

class RegistrationRepository
{
    /** @var EventRepository $event */
    protected $eventRepository;
    /** @var EntityManager $entityManager */
    protected $entityManager;
    /** @var Container $container */
    protected $container;
    /** @var Email $email */
    protected $email;

    public function __construct(EventRepository $event, EntityManager $entityManager, Container $container, $email)
    {
        $this->eventRepository = $event;
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->email = $email;
    }

    private function generateConfirmationNumber(Registration $registration) : void
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
     */
    public function sendConfirmationEmail(Registration $registration, array $badges)
    {
        if ($registration->getEmail() == '' || $registration->getConfirmationnumber() != '') {

            return;
        }
        $this->generateConfirmationNumber($registration);

        if ($this->container->get('kernel')->getEnvironment() == 'dev') {

            return;
        }

        $this->email->sendConfirmationEmail($registration);
    }

    public function generateNumber(Registration $registration)
    {
        $event = $this->eventRepository->getSelectedEvent();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $count = $queryBuilder->select('count(r.registrationId)')
            ->from('AppBundle:Registration', 'r')
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
            ->getRepository('AppBundle:Registration')
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
            ->getRepository('AppBundle:Registration')
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

    /**
     * @param Reggroup $regGroup
     * @param Event $event
     * @return Registration[]
     */
    public function getRegistrationsFromRegGroup(Reggroup $regGroup, Event $event = null) : array
    {
        if (!$event) {
            $event = $this->eventRepository->getSelectedEvent();
        }
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from('AppBundle:Registration', 'r')
            ->innerJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH, 'rrg.registration = r.registrationId')
            ->innerJoin('AppBundle\Entity\Reggroup', 'rg', Join::WITH, 'rg.reggroupId = rrg.reggroup')
            ->where("rg.reggroupId = :reggroupId")
            ->andWhere("r.event = :event")
            ->setParameter('reggroupId', $regGroup)
            ->setParameter('event', $event)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function searchFromManageRegistrations(
        String $searchText,
        $page,
        ?Registrationtype $registrationType,
        ?Registrationstatus $registrationStatus,
        ?Badgetype $badgeType
        ) : array
    {
        $event = $this->eventRepository->getCurrentEvent();
        $page = (int) $page;

        $badgeListQueryBuilder = $this->entityManager->createQueryBuilder();
        $badgeListQueryBuilder->select('GROUP_CONCAT(DISTINCT IDENTITY(sb.badgetype) SEPARATOR \' \')')
            ->from('AppBundle:Badge', 'sb')
            ->where('r.registrationId = sb.registration')
            ;

        $badgeListQuery = $badgeListQueryBuilder->getDQL();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('r.registrationId', 'r.number', 'r.email', 'r.firstname', 'r.lastname', 'r.badgename'
            , 'r.confirmationnumber', 'rs.status', 'e.year', 'r.contactVolunteer', 'r.contactNewsletter'
            , 'COUNT(rrg.registrationreggroupId) as group_count'
            , "($badgeListQuery)"
        )
            ->from('AppBundle:Registration', 'r')
            ->innerJoin('r.registrationstatus', 'rs')
            ->innerJoin('r.event', 'e')
            ->innerJoin('AppBundle\Entity\Badge', 'b', Join::WITH, 'r.registrationId = b.registration')
            ->leftJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH, 'r.registrationId = rrg.registration')


            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('r.number', ':searchText'),
                $queryBuilder->expr()->like('r.lastname', ':searchTextLast'),
                $queryBuilder->expr()->like('r.firstname', ':searchTextFirst'),
                $queryBuilder->expr()->like('r.badgename', ':seatchTextBadge'),
                $queryBuilder->expr()->like('r.email', ':searchTextEmail'),
                $queryBuilder->expr()->like('r.confirmationnumber', ':searchTextConfirmation')
            ))
            ->setParameter('searchText', $searchText)
            ->setParameter('searchTextLast', $searchText . '%')
            ->setParameter('searchTextFirst', $searchText . '%')
            ->setParameter('seatchTextBadge', $searchText . '%')
            ->setParameter('searchTextEmail', $searchText . '%')
            ->setParameter('searchTextConfirmation', $searchText . '%')

            ->andWhere('r.event = :eventId')
            ->setParameter('eventId', $event->getEventId())
        ;

        $queryBuilder
            ->orderBy('r.lastname', 'ASC')
            ->orderBy('r.firstname' , 'ASC')
            ->groupBy('r.registrationId')
        ;

        $results = $queryBuilder->getQuery()->getArrayResult();

        $total_results = count($results);
        $results = array_slice($results, ($page - 1) * 100, 100);
        $returnJson['count_total'] = $total_results;
        $returnJson['count_returned'] = count($results);

        $return_results = array();
        foreach ($results as $result) {
            $Badges = explode(' ', $result['1']);

            $tmp = array();
            $tmp['Registration_ID'] = $result['registrationId'];
            $tmp['ConfirmationNumber'] = $result['confirmationnumber'];
            $tmp['Email'] = $result['email'];
            $tmp['Year'] = $result['year'];
            $tmp['Number'] = $result['number'];
            //$tmp['Badge_Type'] = $result['description'];
            $tmp['FirstName'] = $result['firstname'];
            $tmp['LastName'] = $result['lastname'];
            $tmp['BadgeName'] = $result['badgename'];
            $tmp['Reg_Status'] = $result['status'];
            $tmp['group'] = '';
            if ($result['group_count'] > 0) {
                $tmp['group'] = 'X';
            }
            $tmp['Volunteer'] = '';
            if ($result['contactVolunteer']) {
                $tmp['Volunteer'] = 'X';
            }
            $tmp['Newsletter'] = '';
            if ($result['contactNewsletter']) {
                $tmp['Newsletter'] = 'X';
            }

            $tmp['is_adult'] = 0;
            $tmp['is_minor'] = 0;
            $tmp['is_sponsor'] = 0;
            $tmp['is_comsponsor'] = 0;
            $tmp['is_guest'] = 0;
            $tmp['is_vendor'] = 0;
            $tmp['is_staff'] = 0;
            $tmp['is_exhibitor'] = 0;

            foreach ($Badges as $Badge) {
                switch ($Badge) {
                    case 1:
                        $tmp['is_adult'] = 1;
                        break;
                    case 2:
                        $tmp['is_minor'] = 1;
                        break;
                    case 3:
                        $tmp['is_sponsor'] = 1;
                        break;
                    case 4:
                        $tmp['is_comsponsor'] = 1;
                        break;
                    case 5:
                        $tmp['is_guest'] = 1;
                        break;
                    case 6:
                        $tmp['is_vendor'] = 1;
                        break;
                    case 7:
                        $tmp['is_staff'] = 1;
                        break;
                    case 8:
                        $tmp['is_exhibitor'] = 1;
                        break;
                }
            }
            $return_results[] = $tmp;
        }
        $returnJson['results'] = $return_results;

        return $returnJson;
    }
}