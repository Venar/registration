<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="Event", indexes={@ORM\Index(name="Year", columns={"Year"}), @ORM\Index(name="Active", columns={"Active", "Public"}), @ORM\Index(name="FK1_Event_CreatedBy", columns={"CreatedBy"}), @ORM\Index(name="FK2_Event_ModifiedBy", columns={"ModifiedBy"})})
 * @ORM\Entity
 */
class Event
{
    /**
     * @var string
     *
     * @ORM\Column(name="Year", type="string", length=255, nullable=false)
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="StartDate", type="datetime", nullable=true)
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EndDate", type="datetime", nullable=true)
     */
    private $enddate;

    /**
     * @var string
     *
     * @ORM\Column(name="Theme", type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @var integer
     *
     * @ORM\Column(name="AttendanceCap", type="integer", nullable=false)
     */
    private $attendancecap;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="Public", type="boolean", nullable=false)
     */
    private $public = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="FinalAttendance", type="integer", nullable=false)
     */
    private $finalattendance;

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
     * @ORM\Column(name="Event_ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventId;

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
     * Set year
     *
     * @param string $year
     *
     * @return Event
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set startdate
     *
     * @param \DateTime $startdate
     *
     * @return Event
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     *
     * @return Event
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set theme
     *
     * @param string $theme
     *
     * @return Event
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set attendancecap
     *
     * @param integer $attendancecap
     *
     * @return Event
     */
    public function setAttendancecap($attendancecap)
    {
        $this->attendancecap = $attendancecap;

        return $this;
    }

    /**
     * Get attendancecap
     *
     * @return integer
     */
    public function getAttendancecap()
    {
        return $this->attendancecap;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Event
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return Event
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set finalattendance
     *
     * @param integer $finalattendance
     *
     * @return Event
     */
    public function setFinalattendance($finalattendance)
    {
        $this->finalattendance = $finalattendance;

        return $this;
    }

    /**
     * Get finalattendance
     *
     * @return integer
     */
    public function getFinalattendance()
    {
        return $this->finalattendance;
    }

    /**
     * Set createddate
     *
     * @param \DateTime $createddate
     *
     * @return Event
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
     * @return Event
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
     * Get eventId
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set createdby
     *
     * @param \AppBundle\Entity\User $createdby
     *
     * @return Event
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
     * @return Event
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
