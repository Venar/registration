<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Badgetype;
use AppBundle\Entity\Registration;
use AppBundle\Entity\Registrationstatus;
use AppBundle\Entity\Registrationtype;
use Doctrine\ORM\EntityManager;
use \AppBundle\Entity\Event;
use Doctrine\ORM\Query\Expr\Join;

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

    public function getFromRegistrationId($registrationId)
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
            if ($result['contactVolunteer'] == 'true') {
                $tmp['Volunteer'] = 'X';
            }
            $tmp['Newsletter'] = '';
            if ($result['contactNewsletter'] == 'true') {
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