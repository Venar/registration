<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registrationreggroup
 *
 * @ORM\Table(name="registrationreggroup", indexes={@ORM\Index(name="FK1_RegistrationRegGroup_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_RegistrationRegGroup_ModifiedBy", columns={"ModifiedBy"}), @ORM\Index(name="FK2_RegistrationRegGroup_Registration_ID", columns={"Registration_ID"}), @ORM\Index(name="FK2_RegistrationRegGroup_RegGroup_ID", columns={"RegGroup_ID"})})
 * @ORM\Entity
 */
class Registrationreggroup
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
     * @ORM\Column(name="RegistrationRegGroup_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationreggroupId;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CreatedBy", referencedColumnName="User_ID")
     * })
     */
    private $createdby;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ModifiedBy", referencedColumnName="User_ID")
     * })
     */
    private $modifiedby;

    /**
     * @var \AppBundle\Entity\Reggroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Reggroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RegGroup_ID", referencedColumnName="RegGroup_ID")
     * })
     */
    private $reggroup;

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
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Registrationreggroup
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
     * @return Registrationreggroup
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
     * Get registrationreggroupId
     *
     * @return integer
     */
    public function getRegistrationreggroupId()
    {
        return $this->registrationreggroupId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Registrationreggroup
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
     * @return Registrationreggroup
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
     * Set reggroup
     *
     * @param \AppBundle\Entity\Reggroup $reggroup
     *
     * @return Registrationreggroup
     */
    public function setReggroup(\AppBundle\Entity\Reggroup $reggroup = null)
    {
        $this->reggroup = $reggroup;

        return $this;
    }

    /**
     * Get reggroup
     *
     * @return \AppBundle\Entity\Reggroup
     */
    public function getReggroup()
    {
        return $this->reggroup;
    }

    /**
     * Set registration
     *
     * @param \AppBundle\Entity\Registration $registration
     *
     * @return Registrationreggroup
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
}
