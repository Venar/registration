<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Badge
 *
 * @ORM\Table(name="Badge", indexes={@ORM\Index(name="FK1_Badge_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_Badge_ModifiedBy", columns={"ModifiedBy"}), @ORM\Index(name="FK2_Badge_Registration_ID", columns={"Registration_ID"}), @ORM\Index(name="FK2_Registration_BadgeType_ID", columns={"BadgeType_ID"}), @ORM\Index(name="FK2_Registration_BadgeStatus_ID", columns={"BadgeStatus_ID"})})
 * @ORM\Entity
 */
class Badge
{
    /**
     * @var string
     *
     * @ORM\Column(name="Number", type="string", length=255, nullable=false)
     */
    private $number;

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
     * @ORM\Column(name="Badge_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $badgeId;

    /**
     * @var \AppBundle\Entity\Badgestatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Badgestatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BadgeStatus_ID", referencedColumnName="BadgeStatus_ID")
     * })
     */
    private $badgestatus;

    /**
     * @var \AppBundle\Entity\Badgetype
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Badgetype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BadgeType_ID", referencedColumnName="BadgeType_ID")
     * })
     */
    private $badgetype;

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
     * @var \AppBundle\Entity\Registration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Registration_ID", referencedColumnName="Registration_ID")
     * })
     */
    private $registration;



    /**
     * Set number
     *
     * @param string $number
     *
     * @return Badge
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Badge
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
     * @return Badge
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
     * Get badgeId
     *
     * @return integer
     */
    public function getBadgeId()
    {
        return $this->badgeId;
    }

    /**
     * Set badgestatus
     *
     * @param \AppBundle\Entity\Badgestatus $badgestatus
     *
     * @return Badge
     */
    public function setBadgestatus(\AppBundle\Entity\Badgestatus $badgestatus = null)
    {
        $this->badgestatus = $badgestatus;

        return $this;
    }

    /**
     * Get badgestatus
     *
     * @return \AppBundle\Entity\Badgestatus
     */
    public function getBadgestatus()
    {
        return $this->badgestatus;
    }

    /**
     * Set badgetype
     *
     * @param \AppBundle\Entity\Badgetype $badgetype
     *
     * @return Badge
     */
    public function setBadgetype(\AppBundle\Entity\Badgetype $badgetype = null)
    {
        $this->badgetype = $badgetype;

        return $this;
    }

    /**
     * Get badgetype
     *
     * @return \AppBundle\Entity\Badgetype
     */
    public function getBadgetype()
    {
        return $this->badgetype;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Badge
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
     * @return Badge
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
     * @return Badge
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
