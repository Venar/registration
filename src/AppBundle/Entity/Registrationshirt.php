<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registrationshirt
 *
 * @ORM\Table(name="RegistrationShirt", indexes={@ORM\Index(name="FK1_RegistrationShirt_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_RegistrationShirt_ModifiedBy", columns={"ModifiedBy"}), @ORM\Index(name="FK2_RegistrationShirt_Registration_ID", columns={"Registration_ID"}), @ORM\Index(name="FK2_RegistrationShirt_Shirt_ID", columns={"Shirt_ID"})})
 * @ORM\Entity
 */
class Registrationshirt
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CreatedDate", type="datetime", nullable=true)
     */
    private $createddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ModifiedDate", type="datetime", nullable=true)
     */
    private $modifieddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="RegistrationShirt_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationshirtId;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CreatedBy", referencedColumnName="id")
     * })
     */
    private $createdby;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ModifiedBy", referencedColumnName="id")
     * })
     */
    private $modifiedby;

    /**
     * @var \AppBundle\Entity\Registration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Registration_ID", referencedColumnName="Registration_ID")
     * })
     */
    private $registration;

    /**
     * @var \AppBundle\Entity\Shirt
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Shirt")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Shirt_ID", referencedColumnName="Shirt_ID")
     * })
     */
    private $shirt;



    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Registrationshirt
     */
    public function setCreateddate($createddate)
    {
        $this->createddate = $createddate;

        return $this;
    }

    /**
     * Get createddate
     *
     * @return \DateTime
     */
    public function getCreateddate()
    {
        return $this->createddate;
    }

    /**
     * Set modifieddate
     *
     * @param \DateTime $modifieddate
     *
     * @return Registrationshirt
     */
    public function setModifieddate($modifieddate)
    {
        $this->modifieddate = $modifieddate;

        return $this;
    }

    /**
     * Get modifieddate
     *
     * @return \DateTime
     */
    public function getModifieddate()
    {
        return $this->modifieddate;
    }

    /**
     * Get registrationshirtId
     *
     * @return integer
     */
    public function getRegistrationshirtId()
    {
        return $this->registrationshirtId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Registrationshirt
     */
    public function setCreatedby(\AppBundle\Entity\User $createdby = null)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * Set modifiedby
     *
     * @param \AppBundle\Entity\User $modifiedby
     *
     * @return Registrationshirt
     */
    public function setModifiedby(\AppBundle\Entity\User $modifiedby = null)
    {
        $this->modifiedby = $modifiedby;

        return $this;
    }

    /**
     * Get modifiedby
     *
     * @return \AppBundle\Entity\User
     */
    public function getModifiedby()
    {
        return $this->modifiedby;
    }

    /**
     * Set registration
     *
     * @param \AppBundle\Entity\Registration $registration
     *
     * @return Registrationshirt
     */
    public function setRegistration(\AppBundle\Entity\Registration $registration = null)
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
     * @return Registrationshirt
     */
    public function setShirt(\AppBundle\Entity\Shirt $shirt = null)
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
