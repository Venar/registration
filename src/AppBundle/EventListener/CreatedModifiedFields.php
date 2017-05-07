<?php declare(strict_types=1);

namespace AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;

class CreatedModifiedFields
{
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find(1);

        //$user = $this->userRepository->getFromUserId(1); // TODO Get current user

        $entity->setCreateddate(new \DateTime("now"));
        $entity->setCreatedby($user);

        $entity->setModifieddate(new \DateTime("now"));
        $entity->setModifiedby($user);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find(1);
        //$user = $this->userRepository->getFromUserId(1); // TODO Get current user

        $entity->setModifieddate(new \DateTime("now"));
        $entity->setModifiedby($user);
    }
}