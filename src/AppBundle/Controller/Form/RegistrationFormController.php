<?php

namespace AppBundle\Controller\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationFormController extends Controller
{
    /**
     * @Route("/form/registration", name="form_registration")
     *
     * @return StreamedResponse
     */
    public function getRegistrationForm()
    {
        $pdf = $this->get("white_october.tcpdf")->create();
        $pdf->setEvent($this->get('repository_event')->getSelectedEvent());
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
        //global $WorkOrder;
        $FileName = "Bulk";

        $pdf->AddPage();
        $pdf->Ln(10);
        $pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 224, 194), 'strokeColor'=>array(254, 127, 0)));

        $pdf->SetFont('Bauhaus LT Medium', 'B', 14);
        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(155, 16, 'Standard ($50) Until 1/31');
        $pdf->CheckBox('reg_standard', 16);
        $pdf->cell('15');
        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(100, 16, 'Sponsor ($150)');
        $pdf->CheckBox('reg_sponsor', 16);
        $pdf->cell('70');
        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(165, 16, 'Community Sponsor ($250)');
        $pdf->CheckBox('reg_commsponsor', 16);
        $pdf->Ln(16);

        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(140, 16, 'Standard ($55) 2/1+');
        $pdf->CheckBox('reg_standard_late', 16);
        $pdf->cell('60');
        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(80, 16, 'Add Sponsor Breakfast ($30) (Requires Sponsorship)');
        $pdf->CheckBox('', 16);
        $pdf->cell('60');
        //$pdf->RadioButton('membershiptype', 16);
        $pdf->Cell(165, 16, '');
        $pdf->CheckBox('reg_commsponsor', 16);
        $pdf->Ln(16);

        $field1_width = 570;
        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Membership Type');
        $pdf->Ln(40);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 200;
        $field2_width = 200;
        $field3_width = 150;
        $pdf->TextField('lastname', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField('firstname', $field2_width, 16);
        $pdf->cell('10');
        $pdf->TextField('middlename', $field3_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Last Name');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'First Name');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field3_width, $y);
        $pdf->Cell($field3_width, 16, 'Middle Name');
        $pdf->Ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 300;
        $field2_width = 260;
        $pdf->TextField('address1', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField('address2', $field2_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Postal Mailing Address');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'Address 2 (if needed)');
        $pdf->Ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 400;
        $field2_width = 50;
        $field3_width = 100;
        $pdf->TextField('city', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField('state', $field2_width, 16);
        $pdf->cell('10');
        $pdf->TextField('zip', $field3_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'City');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'State');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field3_width, $y);
        $pdf->Cell($field3_width, 16, 'Zip');
        $pdf->Ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 260;
        $field2_width = 300;
        $pdf->TextField('phone', $field1_width, 16);
        $pdf->cell('10');
        $pdf->TextField('email', $field2_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Phone');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'E-mail');
        $pdf->Ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 140;
        $pdf->TextField('badgename', $field1_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Badge Name');
        $pdf->Ln(18);

        $pdf->SetXY('140', $y-22);
        $pdf->Cell('20');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell($field1_width, 16, 'Badge Name Rules:');
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetXY('140', $y-8);
        $pdf->Cell('20');
        $pdf->Cell('10', 16, '*');
        $pdf->Cell('400', 16, 'Badge names cannot be longer than 20 characters or contain special characters.');
        $pdf->SetXY('140', $y+1);
        $pdf->Cell('20');
        $pdf->Cell('10', 16, '*');
        $pdf->Cell('400', 16, 'If left blank or deemed inappropriate, Registration staff reserves the right to use your first name.');

        $pdf->ln(30);

        $pdf->SetFont('helvetica', 'B', 10);
        $birthday_y = $pdf->GetY();
        $field1_width = 50;
        $field2_width = 50;
        $field3_width = 100;
        $pdf->TextField('birth_month', $field1_width, 16);
        $pdf->cell('10', '16', '/');
        $pdf->TextField('birth_day', $field2_width, 16);
        $pdf->cell('10', '16', '/');
        $pdf->TextField('birth_year', $field3_width, 16);
        $pdf->Ln(16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Date of Birth: (MM/DD/YYYY)');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, '');
        $pdf->cell('10');
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field3_width, $y);
        $pdf->Cell($field3_width, 16, '');
        $pdf->ln('16');
        $old_y = $pdf->GetY();
        $pdf->SetFont('Bauhaus LT Medium', 'B', 8);
        $pdf->SetY($birthday_y);
        $pdf->SetX('250');
        $pdf->Cell('300', 16, 'Date of birth will be used to determine your badge bracket.');
        $pdf->ln('9');

        $pdf->SetY($old_y);
        $pdf->Ln(40);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell(50, 16, 'Mens:', 0, 0, 'R');
        $pdf->Cell(20, 16, 'S', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_S', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'M', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_M', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'L', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_L', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'XLT', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_XLT', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('20');
        $pdf->Cell(20, 16, '2XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_2XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('20');
        $pdf->Cell(20, 16, '2XLT', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_2XLT', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('25');
        $pdf->Cell(20, 16, '3XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_3XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('20');
        $pdf->Cell(20, 16, '3XLT', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_3XLT', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('25');
        $pdf->Cell(20, 16, '4XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('mens_4XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Ln(18);
        $pdf->Cell(50, 16, 'Womens:', 0, 0, 'R');
        $pdf->Cell(20, 16, 'S', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('womens_S', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'M', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('womens_M', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'L', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('womens_L', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('10');
        $pdf->Cell(20, 16, 'XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('womens_XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('44');
        //$pdf->Cell(20, 16, 'XLT', 0, 0, 'R');
        //$pdf->TextField('womens_XLT', 14, 16);
        //$pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell('20');
        $pdf->Cell(20, 16, '2XL', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->TextField('womens_2XL', 14, 16);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Ln(18);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        $field1_width = 570;
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'T-Shirt? $15/shirt (Sponsor/Community Sponsor receive 1 free shirt)');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->ln();
        $pdf->cell('20');
        $pdf->Cell('300', 16, 'Shirts must be paid for with your pre-registration. Please note the additional amount in the total below.');
        $pdf->Ln(30);

        $field1_width = 210;
        $field2_width = 180;
        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $y = $pdf->GetY();
        //$pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Would you like to Volunteer?');
        $pdf->CheckBox('volunteer', 16);
        $pdf->cell('30');
        //$pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field2_width, $y);
        $pdf->Cell($field2_width, 16, 'Receive our Newsletter?');
        $pdf->CheckBox('newsletter', 16);
        $pdf->Ln(40);

        $y = $pdf->GetY();
        $pdf->SetFont('Bauhaus LT Medium', '', 12);
        $pdf->Cell(170, 16, 'Please make checks payable to: ');
        $pdf->SetFont('Bauhaus', 'B', 12);
        $pdf->Cell(100, 16, 'Anime Twin Cities');
        $pdf->Ln(22);
        $pdf->SetFont('Bauhaus LT Medium', '', 12);
        $pdf->Cell(400, 16, 'Mail your check along with this completed form to:');
        $pdf->Ln(22);
        $pdf->SetFont('Bauhaus', 'B', 14);
        $pdf->Cell('10');
        $pdf->Cell(400, 16, 'Anime Twin Cities');
        $pdf->Ln(14);
        $pdf->Cell('10');
        $pdf->Cell(400, 16, 'P.O. Box 48309');
        $pdf->Ln(14);
        $pdf->Cell('10');
        $pdf->Cell(400, 16, 'Coon Rapids, MN 55448');
        $pdf->Ln(22);
        $pdf->SetFont('Bauhaus LT Medium', 'B', 12);
        $pdf->Cell(400, 16, 'Please do not send cash.');


        $pdf->SetFont('helvetica', 'B', 10);
        $field1_width = 200;
        $pdf->SetXY('350', $y+70);
        $pdf->TextField('total_paid', $field1_width, 16);

        $pdf->SetFont('Bauhaus LT Medium', 'B', 16);
        $pdf->SetXY('350', $y+86);
        $y = $pdf->GetY();
        $pdf->Line($pdf->GetX(), $y, $pdf->GetX()+$field1_width, $y);
        $pdf->Cell($field1_width, 16, 'Total Amount Paid');

        return new StreamedResponse(function () use ($pdf) {
            $pdf->Output('ADRegistration.pdf', 'I');
        });
    }
}
