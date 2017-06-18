<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registrationextra
 *
 * @ORM\Table(name="RegistrationExtra", indexes={@ORM\Index(name="FK1_RegistrationExtra_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_RegistrationExtra_ModifiedBy", columns={"ModifiedBy"}), @ORM\Index(name="FK2_RegistrationExtra_Registration_ID", columns={"Registration_ID"}), @ORM\Index(name="FK2_RegistrationExtra_Extra_ID", columns={"ExtraId"})})
 * @ORM\Entity
 */
class Registrationextra
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
     * @ORM\Column(name="RegistrationExtraId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationextraId;

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
     * @var \AppBundle\Entity\Extra
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Extra")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ExtraId", referencedColumnName="ExtraId")
     * })
     */
    private $extra;



    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Registrationextra
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
     * @return Registrationextra
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
     * Get RegistrationextraId
     *
     * @return integer
     */
    public function getRegistrationextraId()
    {
        return $this->registrationextraId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Registrationextra
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
     * @return Registrationextra
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
     * @return Registrationextra
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
     * Set extra
     *
     * @param \AppBundle\Entity\Extra $extra
     *
     * @return Registrationextra
     */
    public function setExtra(\AppBundle\Entity\Extra $extra = null)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra
     *
     * @return \AppBundle\Entity\Extra
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
