<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Repository;


use AppBundle\Entity\EventBadgeType;
use Doctrine\ORM\EntityRepository;

class EventBadgeTypeRepository extends EntityRepository
{
    /**
     * @return EventBadgeType[]
     */
    public function findAll()
    {
        return $this->findBy([], []);
    }
}