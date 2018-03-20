<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="`group`", indexes={@ORM\Index(name="FK1_Group_CreatedBy", columns={"created_by"}), @ORM\Index(name="FK2_Group_ModifiedBy", columns={"modified_by"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupRepository")
 */
class Group
{
    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=255, nullable=false)
     */
    private $school;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=32, nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=16, nullable=false)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="leader", type="string", length=255, nullable=false)
     */
    private $leader;

    /**
     * @var string
     *
     * @ORM\Column(name="leader_phone", type="string", length=255, nullable=false)
     */
    private $leaderPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="leader_email", type="string", length=255, nullable=false)
     */
    private $leaderEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="authorized_name", type="string", length=255, nullable=false)
     */
    private $authorizedName;

    /**
     * @var string
     *
     * @ORM\Column(name="authorized_phone", type="string", length=255, nullable=false)
     */
    private $authorizedPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="authorized_email", type="string", length=255, nullable=false)
     */
    private $authorizedEmail;

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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Registration", mappedBy="groups")
     */
    private $registrations;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * Set leaderPhone
     *
     * @param string $leaderPhone
     *
     * @return Group
     */
    public function setLeaderPhone($leaderPhone)
    {
        $this->leaderPhone = $leaderPhone;

        return $this;
    }

    /**
     * Get leaderphone
     *
     * @return string
     */
    public function getLeaderPhone()
    {
        return $this->leaderPhone;
    }

    /**
     * Set leaderEmail
     *
     * @param string $leaderEmail
     *
     * @return Group
     */
    public function setLeaderEmail($leaderEmail)
    {
        $this->leaderEmail = $leaderEmail;

        return $this;
    }

    /**
     * Get leaderEmail
     *
     * @return string
     */
    public function getLeaderEmail()
    {
        return $this->leaderEmail;
    }

    /**
     * Set authorizedName
     *
     * @param string $authorizedName
     *
     * @return Group
     */
    public function setAuthorizedName($authorizedName)
    {
        $this->authorizedName = $authorizedName;

        return $this;
    }

    /**
     * Get authorizedName
     *
     * @return string
     */
    public function getAuthorizedName()
    {
        return $this->authorizedName;
    }

    /**
     * Set authorizedPhone
     *
     * @param string $authorizedPhone
     *
     * @return Group
     */
    public function setAuthorizedPhone($authorizedPhone)
    {
        $this->authorizedPhone = $authorizedPhone;

        return $this;
    }

    /**
     * Get authorizedPhone
     *
     * @return string
     */
    public function getAuthorizedPhone()
    {
        return $this->authorizedPhone;
    }

    /**
     * Set authorizedEmail
     *
     * @param string $authorizedEmail
     *
     * @return Group
     */
    public function setAuthorizedEmail($authorizedEmail)
    {
        $this->authorizedEmail = $authorizedEmail;

        return $this;
    }

    /**
     * Get authorizedEmail
     *
     * @return string
     */
    public function getAuthorizedEmail()
    {
        return $this->authorizedEmail;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Group
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
     * @return Group
     */
    public function setModifieddate($modifiedDate)
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
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->id;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return Group
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
     * @return Group
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
     * Get registrations
     *
     * @return Registration[]|\Doctrine\Common\Collections\Collection
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    /**
     * @param Registration $registration
     */
    public function addRegistration(Registration $registration)
    {
        $registration->addGroup($this);
        $this->registrations[] = $registration;
    }
}
