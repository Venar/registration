<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Failedlogin
 *
 * @ORM\Table(name="FailedLogin", indexes={@ORM\Index(name="FK1_FailedLogin_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_FailedLogin_ModifiedBy", columns={"ModifiedBy"})})
 * @ORM\Entity
 */
class Failedlogin
{
    /**
     * @var string
     *
     * @ORM\Column(name="Login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var integer
     *
     * @ORM\Column(name="IPAddress", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ipaddress;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Attempted", type="datetime", nullable=false)
     */
    private $attempted;

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
     * @ORM\Column(name="FailedLogin_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $failedloginId;

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
     * Set login
     *
     * @param string $login
     *
     * @return Failedlogin
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set ipaddress
     *
     * @param integer $ipaddress
     *
     * @return Failedlogin
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * Get ipaddress
     *
     * @return integer
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * Set attempted
     *
     * @param \DateTime $attempted
     *
     * @return Failedlogin
     */
    public function setAttempted($attempted)
    {
        $this->attempted = $attempted;

        return $this;
    }

    /**
     * Get attempted
     *
     * @return \DateTime
     */
    public function getAttempted()
    {
        return $this->attempted;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Failedlogin
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
     * @return Failedlogin
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
     * Get failedloginId
     *
     * @return integer
     */
    public function getFailedloginId()
    {
        return $this->failedloginId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Failedlogin
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
     * @return Failedlogin
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
