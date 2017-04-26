<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reggroup
 *
 * @ORM\Table(name="reggroup", indexes={@ORM\Index(name="FK1_RegGroup_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_RegGroup_ModifiedBy", columns={"ModifiedBy"})})
 * @ORM\Entity
 */
class Reggroup
{
    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="School", type="string", length=255, nullable=false)
     */
    private $school;

    /**
     * @var string
     *
     * @ORM\Column(name="Address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="City", type="string", length=255, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="State", type="string", length=32, nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="Zip", type="string", length=16, nullable=false)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="Leader", type="string", length=255, nullable=false)
     */
    private $leader;

    /**
     * @var string
     *
     * @ORM\Column(name="LeaderPhone", type="string", length=255, nullable=false)
     */
    private $leaderphone;

    /**
     * @var string
     *
     * @ORM\Column(name="LeaderEmail", type="string", length=255, nullable=false)
     */
    private $leaderemail;

    /**
     * @var string
     *
     * @ORM\Column(name="AuthorizedName", type="string", length=255, nullable=false)
     */
    private $authorizedname;

    /**
     * @var string
     *
     * @ORM\Column(name="AuthorizedPhone", type="string", length=255, nullable=false)
     */
    private $authorizedphone;

    /**
     * @var string
     *
     * @ORM\Column(name="AuthorizedEmail", type="string", length=255, nullable=false)
     */
    private $authorizedemail;

    /**
     * @var integer
     *
     * @ORM\Column(name="CreatedBy", type="integer", nullable=true)
     */
    private $createdby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CreatedDate", type="datetime", nullable=true)
     */
    private $createddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="ModifiedBy", type="integer", nullable=true)
     */
    private $modifiedby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ModifiedDate", type="datetime", nullable=true)
     */
    private $modifieddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="RegGroup_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reggroupId;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Reggroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set school
     *
     * @param string $school
     *
     * @return Reggroup
     */
    public function setSchool($school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return string
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Reggroup
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Reggroup
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Reggroup
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Reggroup
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set leader
     *
     * @param string $leader
     *
     * @return Reggroup
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     *
     * @return string
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set leaderphone
     *
     * @param string $leaderphone
     *
     * @return Reggroup
     */
    public function setLeaderphone($leaderphone)
    {
        $this->leaderphone = $leaderphone;

        return $this;
    }

    /**
     * Get leaderphone
     *
     * @return string
     */
    public function getLeaderphone()
    {
        return $this->leaderphone;
    }

    /**
     * Set leaderemail
     *
     * @param string $leaderemail
     *
     * @return Reggroup
     */
    public function setLeaderemail($leaderemail)
    {
        $this->leaderemail = $leaderemail;

        return $this;
    }

    /**
     * Get leaderemail
     *
     * @return string
     */
    public function getLeaderemail()
    {
        return $this->leaderemail;
    }

    /**
     * Set authorizedname
     *
     * @param string $authorizedname
     *
     * @return Reggroup
     */
    public function setAuthorizedname($authorizedname)
    {
        $this->authorizedname = $authorizedname;

        return $this;
    }

    /**
     * Get authorizedname
     *
     * @return string
     */
    public function getAuthorizedname()
    {
        return $this->authorizedname;
    }

    /**
     * Set authorizedphone
     *
     * @param string $authorizedphone
     *
     * @return Reggroup
     */
    public function setAuthorizedphone($authorizedphone)
    {
        $this->authorizedphone = $authorizedphone;

        return $this;
    }

    /**
     * Get authorizedphone
     *
     * @return string
     */
    public function getAuthorizedphone()
    {
        return $this->authorizedphone;
    }

    /**
     * Set authorizedemail
     *
     * @param string $authorizedemail
     *
     * @return Reggroup
     */
    public function setAuthorizedemail($authorizedemail)
    {
        $this->authorizedemail = $authorizedemail;

        return $this;
    }

    /**
     * Get authorizedemail
     *
     * @return string
     */
    public function getAuthorizedemail()
    {
        return $this->authorizedemail;
    }

    /**
     * Set createdby
     *
     * @param integer $createdby
     *
     * @return Reggroup
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby
     *
     * @return integer
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Reggroup
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
     * Set modifiedby
     *
     * @param integer $modifiedby
     *
     * @return Reggroup
     */
    public function setModifiedby($modifiedby)
    {
        $this->modifiedby = $modifiedby;

        return $this;
    }

    /**
     * Get modifiedby
     *
     * @return integer
     */
    public function getModifiedby()
    {
        return $this->modifiedby;
    }

    /**
     * Set modifieddate
     *
     * @param \DateTime $modifieddate
     *
     * @return Reggroup
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
     * Get reggroupId
     *
     * @return integer
     */
    public function getReggroupId()
    {
        return $this->reggroupId;
    }
}
