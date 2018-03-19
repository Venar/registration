<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventBadgeType;
use Doctrine\ORM\EntityRepository;

class EventBadgeTypeRepository extends EntityRepository
{
    /**
     * @return EventBadgeType[]
     */
    public function findAll()
    {
        return parent::findBy([], []);
    }
    /**
     * @return EventBadgeType
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param Event     $event
     * @param BadgeType $badgeType
     * @return EventBadgeType
     */
    public function findFromEventAndBadgeType(Event $event, BadgeType $badgeType) : EventBadgeType
    {
        return $this->findOneBy([
            'event' => $event,
            'badgeType' => $badgeType,
        ]);
    }
}