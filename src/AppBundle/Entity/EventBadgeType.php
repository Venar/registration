<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Event
 *
 * @ORM\Table(name="event_badge_type",
 *     indexes={
 *         @ORM\Index(name="FK_EventBadgeType_BadgeType_ID", columns={"badge_type_id"}),
 *         @ORM\Index(name="FK_EventBadgeType_Event_ID", columns={"event_id"}),
 *         @ORM\Index(name="FK_Event_CreatedBy", columns={"created_by"}),
 *         @ORM\Index(name="FK_Event_ModifiedBy", columns={"modified_by"})
 *     },
 *     uniqueConstraints={
 *         @UniqueConstraint(name="event_badgeTypeId_unique",
 *             columns={"badge_type_id", "event_id"})
 *     },
 *     )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventBadgeTypeRepository")
 * @Vich\Uploadable
 */
class EventBadgeType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="artworkPath", type="string", length=255, nullable=true)
     */
    private $artworkPath;

    /**
     * @Vich\UploadableField(mapping="badge_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @var \AppBundle\Entity\Event
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $event;

    /**
     * @var \AppBundle\Entity\BadgeType
     *
     * @ORM\ManyToOne(targetEntity="BadgeType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="badge_type_id", referencedColumnName="id")
     * })
     */
    private $badgeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true)
     */
    private $modifiedDate;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modified_by", referencedColumnName="id")
     * })
     */
    private $modifiedBy;





    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return EventBadgeType
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set badgeType
     *
     * @param \AppBundle\Entity\BadgeType $badgeType
     *
     * @return EventBadgeType
     */
    public function setBadgeType(BadgeType $badgeType = null)
    {
        $this->badgeType = $badgeType;

        return $this;
    }

    /**
     * Get badgeType
     *
     * @return \AppBundle\Entity\BadgeType
     */
    public function getBadgeType()
    {
        return $this->badgeType;
    }

    /**
     * Get eventId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return EventBadgeType
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set created_by
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return EventBadgeType
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     *
     * @return EventBadgeType
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * Set modifiedBy
     *
     * @param \AppBundle\Entity\User $modifiedBy
     *
     * @return EventBadgeType
     */
    public function setModifiedby(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            $this->modifiedDate = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setArtworkPath($image)
    {
        $this->artworkPath = $image;
    }

    public function getArtworkPath()
    {
        return $this->artworkPath;
    }

    /**
     * Alias For EasyAdmin
     *
     * @return string
     */
    public function setImage($image)
    {
        $this->setArtworkPath($image);
    }

    /**
     * Alias For EasyAdmin
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getArtworkPath();
    }
}