<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Reggroup;
use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class RegGroupRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Reggroup';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $reggroupId
     * @return Reggroup|null
     */
    public function getFromReggroupId($reggroupId)
    {
        if (!$reggroupId) {

            return null;
        }

        $regGroup = $this->entityManager
            ->getRepository('AppBundle:Reggroup')
            ->find($reggroupId)
        ;

        if (!$regGroup) {
            /*
            throw $this->createNotFoundException(
                'No registration group found for id '.$reggroupId
            );
            */
        }

        return $regGroup;
    }

    /**
     * @param String $school
     * @return Reggroup[]
     */
    public function findFromSchool(String $school = '') : array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('rg')
            ->from('AppBundle:Reggroup', 'rg');

        if ($school != '') {
            $queryBuilder->where($queryBuilder->expr()->like('rg.school', ':school'))
                ->setParameter('school', "%$school%");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Registration $registration
     * @return Reggroup
     */
    public function getRegGroupFromRegistration(Registration $registration) : ?Reggroup
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('rg')
            ->from('AppBundle:Reggroup', 'rg')
            ->innerJoin('AppBundle\Entity\Registrationreggroup', 'rrg', Join::WITH, 'rrg.reggroup = rg.reggroupId')
            ->innerJoin('AppBundle\Entity\Registration', 'r', Join::WITH, 'r.registrationId = rrg.registration')
            ->where("r.registrationId = :registrationId")
            ->setParameter('registrationId', $registration)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Reggroup[]
     */
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}