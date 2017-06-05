<?php
/**
 * Created by PhpStorm.
 * User: jjkoniges
 * Date: 6/2/17
 * Time: 8:25 PM
 */

namespace AppBundle\Service\TCPDF;


use AppBundle\Entity\Event;

class BadgePDF extends \TCPDF
{
    protected $isLandscape = false;

    /** @var $event Event */
    protected $event;

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function __construct($orientation='P', $unit='mm', $format=array(54, 85.6), $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false, 0);
        $this->SetTextColor(0, 0, 0);

        $preferences = array(
            'Duplex' => 'DuplexFlipLongEdge', // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
            'PickTrayByPDFSize' => true,
            'PrintPageRange' => array(2, 1),
            'NumCopies' => 1
        );

        $this->setViewerPreferences($preferences);
    }

    /**
     * @param bool $isLandscape
     */
    public function setIsLandscape($isLandscape)
    {
        $this->isLandscape = $isLandscape;
    }

    /**
     * @return bool
     */
    public function getIsLandscape()
    {
        return $this->isLandscape;
    }

    public function Header() {
    }

    // Page footer
    public function Footer() {
    }
}