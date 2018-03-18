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

/**
 * RegistrationHistory
 *
 * @ORM\Table(name="history", indexes={@ORM\Index(name="FK1_RegistrationHistory_Registration_ID", columns={"registration_id"}), @ORM\Index(name="FK2_RegistrationHistory_CreatedBy", columns={"created_by"}), @ORM\Index(name="FK3_RegistrationHistory_ModifiedBy", columns={"modified_by"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryRepository")
 */
class History
{
    /**
     * @var string
     *
     * @ORM\Column(name="change_text", type="text", length=65535, nullable=false)
     */
    private $changeText;

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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Registration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="id")
     * })
     */
    private $registration;

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
     * Set changeText
     *
     * @param string $changeText
     *
     * @return History
     */
    public function setChangeText($changeText)
    {
        $this->changeText = $changeText;

        return $this;
    }

    /**
     * Get changeText
     *
     * @return string
     */
    public function getChangeText()
    {
        return $this->changeText;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return History
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
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     *
     * @return History
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
     * Get HistoryId
     *
     * @return integer
     */
    public function getHistoryId()
    {
        return $this->id;
    }

    /**
     * Set registration
     *
     * @param \AppBundle\Entity\Registration $registration
     *
     * @return History
     */
    public function setRegistration(Registration $registration = null)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return \AppBundle\Entity\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return History
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
     * Set modifiedBy
     *
     * @param \AppBundle\Entity\User $modifiedBy
     *
     * @return History
     */
    public function setModifiedBy(User $modifiedBy = null)
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
}
