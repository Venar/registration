<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grouppermission
 *
 * @ORM\Table(name="grouppermission", indexes={@ORM\Index(name="Group_ID", columns={"Group_ID"}), @ORM\Index(name="Permission_ID", columns={"Permission_ID"}), @ORM\Index(name="FK1_GroupPermission_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_GroupPermission_ModifiedBy", columns={"ModifiedBy"})})
 * @ORM\Entity
 */
class Grouppermission
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
     * @ORM\Column(name="GroupPermission_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $grouppermissionId;

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
     * @var \AppBundle\Entity\Group
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Group_ID", referencedColumnName="Group_ID")
     * })
     */
    private $group;

    /**
     * @var \AppBundle\Entity\Permission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Permission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Permission_ID", referencedColumnName="Permission_ID")
     * })
     */
    private $permission;



    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Grouppermission
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
     * @return Grouppermission
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
     * Get grouppermissionId
     *
     * @return integer
     */
    public function getGrouppermissionId()
    {
        return $this->grouppermissionId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Grouppermission
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
     * @return Grouppermission
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
     * Set group
     *
     * @param \AppBundle\Entity\Group $group
     *
     * @return Grouppermission
     */
    public function setGroup(\AppBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AppBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set permission
     *
     * @param \AppBundle\Entity\Permission $permission
     *
     * @return Grouppermission
     */
    public function setPermission(\AppBundle\Entity\Permission $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \AppBundle\Entity\Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
