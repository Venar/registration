<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registration
 *
 * @ORM\Table(name="Registration", indexes={@ORM\Index(name="FK1_Registration_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_Registration_ModifiedBy", columns={"ModifiedBy"}), @ORM\Index(name="FK2_Registration_RegistrationType_ID", columns={"RegistrationType_ID"}), @ORM\Index(name="FK2_Registration_RegistrationStatus_ID", columns={"RegistrationStatus_ID"}), @ORM\Index(name="FK2_Registration_Event_ID", columns={"Event_ID"}), @ORM\Index(name="FK3_TransferedTo", columns={"TransferedTo"})})
 * @ORM\Entity
 */
class Registration
{
    /**
     * @var string
     *
     * @ORM\Column(name="Number", type="string", length=255, nullable=false)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="ConfirmationNumber", type="string", length=255, nullable=true)
     */
    private $confirmationnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="FirstName", type="string", length=255, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="LastName", type="string", length=255, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="MiddleName", type="string", length=255, nullable=false)
     */
    private $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="Address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="Address2", type="string", length=255, nullable=false)
     */
    private $address2;

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
     * @ORM\Column(name="Phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="Email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="BadgeName", type="string", length=255, nullable=false)
     */
    private $badgename;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact_volunteer", type="boolean", nullable=false)
     */
    private $contactVolunteer = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact_newsletter", type="boolean", nullable=false)
     */
    private $contactNewsletter = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="XML", type="string", length=8296, nullable=true)
     */
    private $xml;

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
     * @ORM\Column(name="Registration_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationId;

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
     * @var \AppBundle\Entity\Event
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Event_ID", referencedColumnName="Event_ID")
     * })
     */
    private $event;

    /**
     * @var \AppBundle\Entity\Registrationstatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registrationstatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RegistrationStatus_ID", referencedColumnName="RegistrationStatus_ID")
     * })
     */
    private $registrationstatus;

    /**
     * @var \AppBundle\Entity\Registrationtype
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registrationtype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="RegistrationType_ID", referencedColumnName="RegistrationType_ID")
     * })
     */
    private $registrationtype;

    /**
     * @var \AppBundle\Entity\Registration
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TransferedTo", referencedColumnName="Registration_ID")
     * })
     */
    private $transferedto;

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
     * Set number
     *
     * @param string $number
     *
     * @return Registration
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
     * Set confirmationnumber
     *
     * @param string $confirmationnumber
     *
     * @return Registration
     */
    public function setConfirmationnumber($confirmationnumber)
    {
        $this->confirmationnumber = $confirmationnumber;

        return $this;
    }

    /**
     * Get confirmationnumber
     *
     * @return string
     */
    public function getConfirmationnumber()
    {
        return $this->confirmationnumber;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Registration
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Registration
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     *
     * @return Registration
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Registration
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
     * Set address2
     *
     * @param string $address2
     *
     * @return Registration
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Registration
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
     * @return Registration
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
     * @return Registration
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Registration
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Registration
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Registration
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set badgename
     *
     * @param string $badgename
     *
     * @return Registration
     */
    public function setBadgename($badgename)
    {
        $this->badgename = $badgename;

        return $this;
    }

    /**
     * Get badgename
     *
     * @return string
     */
    public function getBadgename()
    {
        return $this->badgename;
    }

    /**
     * Set contactVolunteer
     *
     * @param boolean $contactVolunteer
     *
     * @return Registration
     */
    public function setContactVolunteer($contactVolunteer)
    {
        $this->contactVolunteer = $contactVolunteer;

        return $this;
    }

    /**
     * Get contactVolunteer
     *
     * @return boolean
     */
    public function getContactVolunteer()
    {
        return $this->contactVolunteer;
    }

    /**
     * Set contactNewsletter
     *
     * @param boolean $contactNewsletter
     *
     * @return Registration
     */
    public function setContactNewsletter($contactNewsletter)
    {
        $this->contactNewsletter = $contactNewsletter;

        return $this;
    }

    /**
     * Get contactNewsletter
     *
     * @return boolean
     */
    public function getContactNewsletter()
    {
        return $this->contactNewsletter;
    }

    /**
     * Set xml
     *
     * @param string $xml
     *
     * @return Registration
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get xml
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Registration
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
     * @return Registration
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
     * Get registrationId
     *
     * @return integer
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Registration
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
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Registration
     */
    public function setEvent(\AppBundle\Entity\Event $event = null)
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
     * Set registrationstatus
     *
     * @param \AppBundle\Entity\Registrationstatus $registrationstatus
     *
     * @return Registration
     */
    public function setRegistrationstatus(\AppBundle\Entity\Registrationstatus $registrationstatus = null)
    {
        $this->registrationstatus = $registrationstatus;

        return $this;
    }

    /**
     * Get registrationstatus
     *
     * @return \AppBundle\Entity\Registrationstatus
     */
    public function getRegistrationstatus()
    {
        return $this->registrationstatus;
    }

    /**
     * Set registrationtype
     *
     * @param \AppBundle\Entity\Registrationtype $registrationtype
     *
     * @return Registration
     */
    public function setRegistrationtype(\AppBundle\Entity\Registrationtype $registrationtype = null)
    {
        $this->registrationtype = $registrationtype;

        return $this;
    }

    /**
     * Get registrationtype
     *
     * @return \AppBundle\Entity\Registrationtype
     */
    public function getRegistrationtype()
    {
        return $this->registrationtype;
    }

    /**
     * Set transferedto
     *
     * @param \AppBundle\Entity\Registration $transferedto
     *
     * @return Registration
     */
    public function setTransferedto(\AppBundle\Entity\Registration $transferedto = null)
    {
        $this->transferedto = $transferedto;

        return $this;
    }

    /**
     * Get transferedto
     *
     * @return \AppBundle\Entity\Registration
     */
    public function getTransferedto()
    {
        return $this->transferedto;
    }

    /**
     * Set modifiedby
     *
     * @param \AppBundle\Entity\User $modifiedby
     *
     * @return Registration
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
