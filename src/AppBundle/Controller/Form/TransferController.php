<?php
/**
 * Copyright (c) 2018. Anime Twin Cities, Inc.
 *
 * This project, including all of the files and their contents, is licensed under the terms of MIT License
 *
 * See the LICENSE file in the root of this project for details.
 */

namespace AppBundle\Controller\Form;

use AppBundle\Entity\Event;
use AppBundle\Service\TCPDF\RegistrationPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransferController extends Controller
{
    /**
     * @Route("/form/transfer", name="form_transfer")
     *
     * @return StreamedResponse
     */
    public function getTransferForm()
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        /** @var RegistrationPDF $pdf */
        $pdf = $this->get("white_october.tcpdf")->create();
        $pdf->setEvent($event);
        $pdf->setSubTitle('Registration Form');

        $pdf->setFontSubsetting(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // margin above header
        $pdf->SetHeaderMargin(0);
        $pdf->SetLeftMargin(13);
        $pdf->SetRightMargin(5);
        $pdf->SetAutoPageBreak(true, 45);

        //tcpdf set some language-dependent strings -- ugg why no default?
        global $l;
        $pdf->setLanguageArray($l);

        // set font
        $pdf->SetFont('helvetica', '', 10);

        $pdf->AddPage();
        $pdf->Ln(10);
        $pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 224, 194), 'strokeColor'=>array(254, 127, 0)));

        $pdf = self::membershipInfo($pdf, true);

        $pdf->Ln(40);

        $notice = "By signing below, you authorize the transfer of your membership to the person defined"
            ." in section 'Original Membership Holder'. The person in section 'Transfer Recipient' "
            ." will be able to sign for and pick up your badge.";

        $field1_width = 570;
        $y = $pdf->GetY();
        $pdf->SetFont('Bauhaus LT Medium', '', 12);
        //$pdf->Cell($field1_width, 16, $notice);
        $pdf->MultiCell($field1_width, 12, $notice, 0, '', 0, 1, '', '', true);
        $pdf->Ln(16);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 200;
        $field2_width = 200;
        $pdf->TextField('signature', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField('date', $field2_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Signature');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'Date');
        $pdf->Ln(24);

        $notice = 'Your Signature (Original Membership Holder) We will not process the transfer without this signature!';
        $field1_width = 570;
        $y = $pdf->GetY();
        $pdf->SetFont('Bauhaus LT Medium', '', 12);
        //$pdf->Cell($field1_width, 16, $notice);
        $pdf->MultiCell($field1_width, 12, $notice, 0, '', 0, 1, '', '', true);
        $pdf->Ln(12);

        $field1_width = 570;
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4));
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+$field1_width, $pdf->GetY());
        $pdf->Ln(12);
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0));

        $pdf = self::membershipInfo($pdf, false);

        $pdf->Ln(20);

        $notice = " Your ID(Transfer Recipient) will be required to be shown before badge will be issued and must match"
            ." the name entered above. I also understand that the transfer will be done at the discretion of the"
            ." registration directors and is not guaranteed.";

        $field1_width = 570;
        $pdf->SetFont('Bauhaus LT Medium', '', 12);
        //$pdf->Cell($field1_width, 16, $notice);
        $pdf->MultiCell($field1_width, 12, $notice, 0, '', 0, 1, '', '', true);
        $pdf->Ln(16);

        return $pdf->Output('ADTransfer.pdf', 'I');
    }

    static function membershipInfo($pdf, $originalHolder = false) {
        /* @var $pdf RegistrationPDF */
        $fieldPrefix = 'transfer_';
        $title = 'Transfer Recipient';
        if ($originalHolder) {
            $fieldPrefix = 'original_';
            $title = 'Original Membership Holder';
        }

        $field1_width = 570;
        $pdf->SetFont('Bauhaus', 'B', 16);
        $pdf->Cell($field1_width, 16, "$title: ");
        $pdf->Ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 200;
        $field2_width = 200;
        $field3_width = 150;
        $pdf->TextField($fieldPrefix . 'lastname', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField($fieldPrefix . 'firstname', $field2_width, 16);
        $pdf->cell('10');
        $pdf->TextField($fieldPrefix . 'middlename', $field3_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Last Name');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field2_width, $y);
        $pdf->Cell($field2_width, 16, 'First Name');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field3_width, $y);
        $pdf->Cell($field3_width, 16, 'Middle Name');
        $pdf->Ln(30);

        $birthday_x = $pdf->GetX();
        if (!$originalHolder) {
            $pdf->SetFont('helvetica', 'B', 10);
            $field1_width = 300;
            $field2_width = 260;
            $pdf->TextField($fieldPrefix . 'address1', $field1_width, 16);
            $pdf->cell('10');
            $pdf->TextField($fieldPrefix . 'address2', $field2_width, 16);
            $pdf->Ln(16);

            $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
            $y = $pdf->GetY();
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field1_width, $y);
            $pdf->Cell($field1_width, 16, 'Postal Mailing Address');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field2_width, $y);
            $pdf->Cell($field2_width, 16, 'Address 2 (if needed)');
            $pdf->Ln(30);

            $pdf->SetFont('helvetica', 'B', 10);
            $field1_width = 400;
            $field2_width = 50;
            $field3_width = 100;
            $pdf->TextField($fieldPrefix . 'city', $field1_width, 16);
            $pdf->cell('10');
            $pdf->TextField($fieldPrefix . 'state', $field2_width, 16);
            $pdf->cell('10');
            $pdf->TextField($fieldPrefix . 'zip', $field3_width, 16);
            $pdf->Ln(16);

            $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
            $y = $pdf->GetY();
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field1_width, $y);
            $pdf->Cell($field1_width, 16, 'City');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field2_width, $y);
            $pdf->Cell($field2_width, 16, 'State');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field3_width, $y);
            $pdf->Cell($field3_width, 16, 'Zip');
            $pdf->Ln(30);
        }

        if ($originalHolder) {
            $pdf->SetFont('helvetica', 'B', 10);
            $birthday_x = $pdf->GetX();
            $field1_width = 50;
            $field2_width = 50;
            $field3_width = 100;
            $field4_width = 300;
            $pdf->TextField($fieldPrefix.'birth_month', $field1_width, 16);
            $pdf->cell('10', '16', '/');
            $pdf->TextField($fieldPrefix.'birth_day', $field2_width, 16);
            $pdf->cell('10', '16', '/');
            $pdf->TextField($fieldPrefix.'birth_year', $field3_width, 16);
            $pdf->cell('10');
            $pdf->TextField($fieldPrefix . 'email', $field4_width, 16);
            $pdf->Ln(16);

            $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
            $y = $pdf->GetY();
            $pdf->setX($birthday_x);
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
            $pdf->Cell($field1_width, 16, 'Date of Birth: (MM/DD/YYYY)');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
            $pdf->Cell($field2_width, 16, '');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field3_width, $y);
            $pdf->Cell($field3_width, 16, '');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field4_width, $y);
            $pdf->Cell($field4_width, 16, 'E-mail');
            $pdf->Ln(30);
        } else {
            $pdf->SetFont('helvetica', 'B', 10);
            $field1_width = 260;
            $field2_width = 300;
            $pdf->TextField($fieldPrefix . 'phone', $field1_width, 16);
            $pdf->cell('10');
            $pdf->TextField($fieldPrefix . 'email', $field2_width, 16);
            $pdf->Ln(16);

            $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
            $y = $pdf->GetY();
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field1_width, $y);
            $pdf->Cell($field1_width, 16, 'Phone');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field2_width, $y);
            $pdf->Cell($field2_width, 16, 'E-mail');
            $pdf->Ln(30);
        }

        if (!$originalHolder) {
            $pdf->SetFont('helvetica', 'B', 10);
            $birthday_x = $pdf->GetX();
            $field1_width = 50;
            $field2_width = 50;
            $field3_width = 100;
            $pdf->TextField($fieldPrefix . 'birth_month', $field1_width, 16);
            $pdf->cell('10', '16', '/');
            $pdf->TextField($fieldPrefix . 'birth_day', $field2_width, 16);
            $pdf->cell('10', '16', '/');
            $pdf->TextField($fieldPrefix . 'birth_year', $field3_width, 16);
            $pdf->cell('10');
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $confirmation_width = 140;
        $pdf->TextField($fieldPrefix.'confirmationNumber', $confirmation_width, 16);
        $preY = $pdf->getY();

        if ($originalHolder) {
            $pdf->cell('10');
            $notice = 'Your confirmation number was emailed to the email given at the time of registration.'
                .' If you cannot find your confirmation number, contact ad_register@animedetour.com. You can'
                .' validate if a confirmation number is valid at http://www.animedetour.com/regtransfer';
            $notice_width = 400;
            $pdf->SetFont('Bauhaus LT Medium', '', 13);
            //$pdf->Cell($field1_width, 16, $notice);
            $pdf->MultiCell($notice_width, 12, $notice, 0, '', 0, 1, '', '', true);
        }
        $pdf->setY($preY);
        $pdf->Ln(16);

        if (!$originalHolder) {
            $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
            $y = $pdf->GetY();
            $pdf->setX($birthday_x);
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field1_width, $y);
            $pdf->Cell($field1_width, 16, 'Date of Birth: (MM/DD/YYYY)');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field2_width, $y);
            $pdf->Cell($field2_width, 16, '');
            $pdf->cell('10');
            $pdf->Line($pdf->GetX(), $y, $pdf->GetX() + $field3_width, $y);
            $pdf->Cell($field3_width, 16, '');
        }

        $confirmationBadge = 'Badge Name';
        if ($originalHolder) {
            $confirmationBadge = 'Confirmation Number';
        } else {
            $pdf->cell('10');
        }

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$confirmation_width, $y);
        $pdf->Cell($field1_width, 16, $confirmationBadge);
        $pdf->cell('40');
        $pdf->ln(16);

        return $pdf;
    }
}
