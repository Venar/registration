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
 * RegistrationShirt
 *
 * @ORM\Table(name="registration_shirt",
 *     indexes={
 *     @ORM\Index(name="FK1_RegistrationShirt_CreatedBy", columns={"created_by"}),
 *     @ORM\Index(name="FK2_RegistrationShirt_ModifiedBy", columns={"modified_by"}),
 *     @ORM\Index(name="FK2_RegistrationShirt_Registration_ID", columns={"registration_id"}),
 *     @ORM\Index(name="FK2_RegistrationShirt_Shirt_ID", columns={"shirt_id"})})
 * @ORM\Entity
 */
class RegistrationShirt
{
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
    private $registrationShirtId;

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
     * @var \AppBundle\Entity\Registration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registration", inversedBy="registrationShirts")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id")
     */
    private $registration;

    /**
     * @var \AppBundle\Entity\Shirt
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Shirt", inversedBy="registrationShirts")
     * @ORM\JoinColumn(name="shirt_id", referencedColumnName="id")
     */
    private $shirt;



    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return RegistrationShirt
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
     * @return RegistrationShirt
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
     * Get registrationShirtId
     *
     * @return integer
     */
    public function getRegistrationShirtId()
    {
        return $this->registrationShirtId;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return RegistrationShirt
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
     * @return RegistrationShirt
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

    /**
     * Set registration
     *
     * @param \AppBundle\Entity\Registration $registration
     *
     * @return RegistrationShirt
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
     * Set shirt
     *
     * @param \AppBundle\Entity\Shirt $shirt
     *
     * @return RegistrationShirt
     */
    public function setShirt(Shirt $shirt = null)
    {
        $this->shirt = $shirt;

        return $this;
    }

    /**
     * Get shirt
     *
     * @return \AppBundle\Entity\Shirt
     */
    public function getShirt()
    {
        return $this->shirt;
    }
}
