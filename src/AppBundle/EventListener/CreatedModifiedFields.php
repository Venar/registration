<?php declare(strict_types=1);

namespace AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;

class CreatedModifiedFields
{
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!method_exists($entity, 'setCreateddate')) {
            return;
        }

        $userId = 1; // Default to 1 if not logged in. So the APIs that insert users will have an id.
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find($userId);

        $entity->setCreateddate(new \DateTime("now"));
        $entity->setCreatedby($user);

        $entity->setModifieddate(new \DateTime("now"));
        $entity->setModifiedby($user);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!method_exists($entity, 'setModifieddate')) {
            return;
        }

        $userId = 1; // Default to 1 if not logged in. So the APIs that insert users will have an id.
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find($userId);

        $entity->setModifieddate(new \DateTime("now"));
        $entity->setModifiedby($user);
    }
}