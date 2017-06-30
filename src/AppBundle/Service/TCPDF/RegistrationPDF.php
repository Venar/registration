<?php
/**
 * Created by PhpStorm.
 * User: John J. Koniges
 * Date: 5/31/2017
 * Time: 2:05 PM
 */

namespace AppBundle\Service\TCPDF;

use AppBundle\Entity\Event;

class RegistrationPDF extends \TCPDF
{
    protected $subTitle = '';
    /** @var $event Event */
    protected $event;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    protected function getEvent()
    {
        return $this->event;
    }

    public function __construct($orientation='P', $unit='pt', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        $fontDir = __DIR__ . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'Resources' . DIRECTORY_SEPARATOR
            . 'fonts' . DIRECTORY_SEPARATOR
        ;
        //$this->addFont('BauhausLT-Bold', 'bauhaus', $fontDir . 'bauhauslt');
        //$this->addFont('BauhausLT-Medium', 'bauhausltmedium', $fontDir . 'bauhausltmedium');

        parent::__construct($orientation, $unit, $format);
    }

    public function setSubTitle($subTitle) {
        $this->subTitle = $subTitle;
    }

    public function Header() {
        $year = '';
        $start = '';
        $end = '';
        $event = $this->getEvent();
        if ($event) {
            $year = $event->getYear();
            $start = $event->getStartdate()->format('F jS');
            $end = $event->getEnddate()->format('F jS');
        }

        // Logo
        $this->SetY($this->GetY() + 5);
        $this->Image('images/adlogo.jpg', 150, $this->GetY(), 330);

        $this->ln(50);
        $this->SetFont('Bauhaus LT Medium', 'B', 16);
        $this->MultiCell(612, 0, "Anime Detour $year {$this->subTitle}", 0, 'C');

        $this->ln(-3);
        $this->SetFont('Bauhaus LT Medium', 'B', 10);
        $this->MultiCell(612, 0, $start.' - '.$end.' @ the Hyatt Regency, Minneapolis MN', 0, 'C');
        //$this->Cell(170,10, 'Anime Detour', 0, 0, 'C');

        $this->SetLeftMargin(5);

        // double lines
        /*
       $this->SetY(85);
       $this->Line(0,$this->GetY(),900,$this->GetY());
       $this->SetY($this->GetY() + 2);
       $this->Line(0,$this->GetY(),900,$this->GetY());
       */
        $this->SetTopMargin($this->GetY()+5);

    }

    // Page footer
    public function Footer() {

        $this->SetY(27 * -1);

        $this->SetFont('helvetica', '', 7);


        $this->SetY(-36);

        // Lines
        /*
        $this->Line(0,$this->GetY(),612,$this->GetY());
        $this->SetY($this->GetY()+2);
        $this->Line(0,$this->GetY(),612,$this->GetY());
        */

        $this->SetY($this->GetY()+5);
        $this->Image('images/atc_logo_small.jpg', 10, $this->GetY(), 60);

        $this->SetXY(205, $this->GetY()+3);
        // Page Number
        $this->SetFont('helvetica', 'I', 8);
        if (empty($this->pagegroups)) {
            $pagenumtxt = $this->getAliasNumPage().' of '.$this->getAliasNbPages();
        } else {
            $pagenumtxt = $this->getPageNumGroupAlias().' of '.$this->getPageGroupAlias();
        }
        $this->Cell(200, 10, '            Page '.$pagenumtxt, 0, 0, 'C');

        // Printed Date
        $this->SetFont('helvetica', '', 8);
        $this->Cell(205, 10, \TCPDF_FONTS::unichr(169)." Anime Twin Cities " . date("Y"), 0, 0, 'R');

    }

    // Colored table
    public function ColoredTable($Data, $width = array(), $heightMatchWidth = false) {
        // Colors, line width and bold font
        if ( count($Data) == 0 ) {
            return;
        }
        $title = '';
        $headers = array_keys($Data[0]);

        $this->SetFillColor(158,197,218);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(76, 123, 149);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        if ( count($width) == 0 ) {
            $width = array();
            $HeaderCount = count($headers);
            foreach ( $headers as $title) {
                $width[$title] = 580 / $HeaderCount;
            }
        }
        foreach ( $headers as $title) {
            $this->Cell($width[$title], 7, $title, 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(222,235,242);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Data
        $fill = 0;
        foreach($Data as $row) {
            $height = $this->getFontSize() * $this->getCellHeightRatio();
            $maxRows = 1;
            foreach ( $row as $title => $cell) {
                $rows = $this->getNumLines($cell, $width[$title]);
                if ( $rows > 1 && ( $rows > $maxRows) ) {
                    $maxRows = $rows;
                }
                //echo $Title,"::",$Cell,"::",$rows,"::",$MaxRows,"<br>";
            }

            if ( $heightMatchWidth ) {
                $height = $width[$title];
            } else  {
                $height = $height * $maxRows;
            }
            //var_dump($this->getPageHeight());

            //echo $this->GetY(),"::",$Height, "::",implode("|",$Row), "<br>";
            if ( $this->getPageHeight() < $this->GetY() + $height + 30 ) {
                $this->AddPage();
                $this->SetFillColor(158,197,218);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('', 'B');
                // Header
                foreach ( $headers as $title) {
                    $this->Cell($width[$title], 7, $title, 1, 0, 'C', 1);
                }
                $this->Ln();
                // Color and font restoration
                $this->SetFillColor(222,235,242);
                $this->SetTextColor(0);
                $this->SetFont('');
            }

            //echo implode("|",$Row),"::",$MaxRows,"::",$Height,"<br>";
            foreach ( $row as $title => $cell) {
                if ( strpos($cell, "IMAGE:") === 0 ) {
                    $info = $this->Image(substr($cell, 6),$this->GetX() + 5, $this->GetY() + 5, $height - 10,$width[$title] - 10);
                    $this->MultiCell($width[$title], $height, '', 'LR', 'L', $fill, 0, '', '', true);
                } else if ( is_numeric($cell) ) {
                    $this->MultiCell($width[$title], $height, number_format($cell), 'LR', 'R', $fill, 0, '', '', true);
                } else {
                    $this->MultiCell($width[$title], $height, $cell, 'LR', 'L', $fill, 0, '', '', true);
                }
                //echo $Title,"::",$Cell,"::",$Height,"<br>";
            }
            $this->MultiCell(1, $height, "", 'L', 'L', 0, 1);
            //$this->Ln($Height, true);

            $fill=!$fill;
        }
        $this->Cell(array_sum($width), 0, '', 'T');
    }

    function CellPair($header, $value, $headerWidth = 145, $valueWidth = 145, $fill = 1) {
        $this->SetFillColor(158,197,218);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(76, 123, 149);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $this->Cell($headerWidth , 7, $header, 1, 0, 'L', 1);

        $this->SetFillColor(222,235,242);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->MultiCell($valueWidth, 7, $value, 1, 'L', $fill, 0);
    }
}