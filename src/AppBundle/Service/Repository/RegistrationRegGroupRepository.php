<?php declare(strict_types=1);

namespace AppBundle\Service\Repository;


use AppBundle\Entity\Registrationreggroup;
use AppBundle\Entity\Registration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;


class RegistrationRegGroupRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    const entityName = 'AppBundle:Reggroup';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $RegistrationreggroupId
     * @return Registrationreggroup|null
     */
    public function getFromRegistrationreggroupId($RegistrationreggroupId)
    {
        if (!$RegistrationreggroupId) {

            return null;
        }

        $Registrationreggroup = $this->entityManager
            ->getRepository('AppBundle:Registrationreggroup')
            ->find($RegistrationreggroupId)
        ;

        if (!$Registrationreggroup) {
            /*
            throw $this->createNotFoundException(
                'No registration group found for id '.$reggroupId
            );
            */
        }

        return $Registrationreggroup;
    }

    /**
     * @param Registration $registration
     * @return Registrationreggroup[]
     */
    public function getRegistrationRegGroupFromRegistration(Registration $registration) : array
    {
        if (!$registration) {

            return [];
        }

        $Registrationreggroup = $this->entityManager
            ->getRepository(self::entityName)
            ->findBy(
                array('registration' => $registration)
            );

        return $Registrationreggroup;
    }

    /**
     * @return Registrationreggroup[]
     */
    public function findAll() : array
    {
        return $this->entityManager->getRepository(self::entityName)->findAll();
    }
}