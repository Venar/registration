<?php

namespace AppBundle\Service\TCPDF;


use AppBundle\Entity\Event;
use Doctrine\ORM\EntityManager;

class PDFRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return BadgePDF
     */
    public function getBadgePDF()
    {
        $event = $this->entityManager->getRepository(Event::class)->getSelectedEvent();
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