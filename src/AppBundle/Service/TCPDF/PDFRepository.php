<?php
/**
 * Created by PhpStorm.
 * User: jjkoniges
 * Date: 6/2/17
 * Time: 10:18 PM
 */

namespace AppBundle\Service\TCPDF;


use AppBundle\Service\Repository\EventRepository;

class PDFRepository
{
    /** @var EventRepository $event */
    protected $event;

    public function __construct(EventRepository $event)
    {
        $this->event = $event;
    }

    /**
     * @return BadgePDF
     */
    public function getBadgePDF()
    {
        $event = $this->event->getSelectedEvent();
        $imageFile = "images/badge_backgrounds/{$event->getYear()}/" . 'ADREGSTANDARD.jpg';

        $size = GetImageSize($imageFile); // Read the size
        $width = $size[0];
        $height = $size[1];

        $layout = 'L';
        $badgeSize = [85.6, 54];
        $landscape = true;
        if ($width < $height) {
            $layout = 'P';
            $badgeSize = [54, 85.6];
            $landscape = false;
        }
        $pdf =  new BadgePDF($layout, 'mm', $badgeSize);
        $pdf->setIsLandscape($landscape);
        $pdf->setEvent($event);

        return $pdf;
    }
}