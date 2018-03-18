<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreatedModifiedFields
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!method_exists($entity, 'setCreatedDate')) {
            return;
        }

        $userId = 1; // Default to 1 if not logged in. So the APIs that insert users will have an id.
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find($userId);

        $entity->setCreatedDate(new \DateTime("now"));
        $entity->setCreatedBy($user);

        $entity->setModifiedDate(new \DateTime("now"));
        $entity->setModifiedBy($user);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!method_exists($entity, 'setModifiedDate')) {
            return;
        }

        $userId = 1; // Default to 1 if not logged in. So the APIs that insert users will have an id.
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $user = $args->getEntityManager()->getRepository('AppBundle:User')->find($userId);

        $entity->setModifiedDate(new \DateTime("now"));
        $entity->setModifiedBy($user);
    }
}