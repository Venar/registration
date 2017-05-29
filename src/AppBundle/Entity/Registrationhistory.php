<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registrationhistory
 *
 * @ORM\Table(name="RegistrationHistory", indexes={@ORM\Index(name="FK1_RegistrationHistory_Registration_ID", columns={"Registration_ID"}), @ORM\Index(name="FK2_RegistrationHistory_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK3_RegistrationHistory_ModifiedBy", columns={"ModifiedBy"})})
 * @ORM\Entity
 */
class Registrationhistory
{
    /**
     * @var string
     *
     * @ORM\Column(name="ChangeText", type="text", length=65535, nullable=false)
     */
    private $changetext;

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
     * @ORM\Column(name="RegistrationHistory_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationhistoryId;

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
     * Set changetext
     *
     * @param string $changetext
     *
     * @return Registrationhistory
     */
    public function setChangetext($changetext)
    {
        $this->changetext = $changetext;

        return $this;
    }

    /**
     * Get changetext
     *
     * @return string
     */
    public function getChangetext()
    {
        return $this->changetext;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Registrationhistory
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
     * @return Registrationhistory
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
     * Get registrationhistoryId
     *
     * @return integer
     */
    public function getRegistrationhistoryId()
    {
        return $this->registrationhistoryId;
    }

    /**
     * Set registration
     *
     * @param \AppBundle\Entity\Registration $registration
     *
     * @return Registrationhistory
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
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Registrationhistory
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
     * @return Registrationhistory
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
}
