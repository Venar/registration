<?php

namespace AppBundle\Controller\Printing;

use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeType;
use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use AppBundle\Service\TCPDF\BadgePDF;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class PrintingController extends Controller
{
    /** @var $pdf BadgePDF */
    protected $pdf;

    protected function setUpPDF()
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

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

        $this->pdf = new BadgePDF($layout, 'mm', $badgeSize);
        $this->pdf->setEvent($event);
        $this->pdf->setIsLandscape($landscape);
    }

    /**
     * @Route("/print/bulk/{type}", name="printing_bulk_WithType")
     * @Route("/print/bulk/{type}/{page}", name="printing_bulk_WithType_WithPage")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $type
     * @param String $page
     * @return Response
     */
    public function printingList($type, $page = '1')
    {
        if (!is_numeric($page)) {
            $page = 1;
        }
        $page = (int)$page;

        return $this->printBulk($type, $page);
    }


    /**
     * @Route("/print/single/{registrationId}", name="printing_single")
     * @Route("/print/single/{registrationId}/{badgeId}", name="printing_single_WithSingleBadge")
     * @Security("has_role('ROLE_USER')")
     *
     * @param String $registrationId
     * @param String $badgeId
     * @return Response
     */
    public function printSinglePage($registrationId, $badgeId = '')
    {
        $this->setUpPDF();

        $registration = $this->getDoctrine()->getRepository(Registration::class)->find($registrationId);
        if (!$registration) {

            return new Response('Invalid Registration ID', 500);
        }

        $badge = $this->getDoctrine()->getRepository(Badge::class)->find($badgeId);

        return $this->printSingle($registration, $badge);
    }

    /**
     * @param String $type
     * @param int $page
     * @return mixed
     */
    protected function printBulk($type, $page)
    {
        $this->setUpPDF();

        $badgeTypeRepository = $this->getDoctrine()->getRepository(BadgeType::class);
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();
        $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();

        $limit = 350;
        $offset = ($page - 1) * $limit;
        $order = [];

        $badgeTypes = [];
        $show_group = false;
        switch ($type) {
            case 'staff':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('STAFF');
                break;
            case 'sponsor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGSPONSOR');
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGCOMMSPONSOR');
                break;
            case 'standard':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('ADREGSTANDARD');
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('MINOR');
                break;
            case 'group':
                $show_group = true;
                $order[] = ['groupName', 'ASC'];
                break;
            case 'guest':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('GUEST');
                break;
            case 'exhibitor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('EXHIBITOR');
                break;
            case 'vendor':
                $badgeTypes[] = $badgeTypeRepository->getBadgeTypeFromType('VENDOR');
                break;
        }

        $badgesSubQuery = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
            ->select('IDENTITY(b2.registration)')
            ->from(Badge::class, 'b2');

        for ($i = 0; $i < count($badgeTypes); $i++) {
            if ($i == 0) {
                $badgesSubQuery
                    ->where("b2.badgeType = :type$i");
            } else {
                $badgesSubQuery
                    ->orWhere("b2.badgeType = :type$i");
            }
            $queryBuilder->setParameter("type$i", $badgeTypes[$i]);
        }
        $badgesSubQueryDQL = $badgesSubQuery->getDQL();

        $queryBuilder
            ->select([
                'r.number',
                'r.badgeName',
                'b.number as badgeNumber',
                'bt.name as type',
                'g.name as groupName',
                'r.confirmationNumber'
            ])
            ->from(Registration::class, 'r')
            ->innerJoin('r.registrationStatus', 'rs')
            ->innerJoin('r.badges', 'b')
            ->innerJoin('b.badgeStatus', 'bs')
            ->innerJoin('b.badgeType', 'bt')
            ->leftJoin('r.groups', 'g')
            ->where('rs.active = :active')
            ->andWhere($queryBuilder->expr()->in('r.id', $badgesSubQueryDQL))
            ->andWhere('r.event = :event')
            ->andWhere('bs.active = :bsActive')
            ->setParameter('event', $event)
            ->setParameter('active', true)
            ->setParameter('bsActive', true);


        if ($type != 'staff') {
            $staffBadge = $badgeTypeRepository->getBadgeTypeFromType('STAFF');

            $allStaffBadges = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder()
                ->select('IDENTITY(b3.registration)')
                ->from(Badge::class, 'b3')
                ->where("b3.badgeType = :staffType")
                ->innerJoin('b3.registration', 'b3r')
                ->andWhere('b3r.event = :b3rEvent')
                ->getDQL();

            $queryBuilder->andWhere($queryBuilder->expr()->notIn('r.id', $allStaffBadges));
            $queryBuilder->setParameter('staffType', $staffBadge)
                ->setParameter('b3rEvent', $event);
        }

        if ($show_group) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('g.id'));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('g.id'));
        }

        $order[] = ['r.number', 'ASC'];
        $order[] = ['bt.id', 'DESC'];
        for ($i = 0; $i < count($order); $i++) {
            if ($i == 0) {
                $queryBuilder->orderBy($order[$i][0], $order[$i][1]);
            } else {
                $queryBuilder->addOrderBy($order[$i][0], $order[$i][1]);
            }
        }

        $registrations = $queryBuilder
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getArrayResult();

        foreach ($registrations as $registration) {
            $regNumber = $registration['number'];
            $regName = $registration['badgeName'];
            $badgeNumber = $registration['badgeNumber'];
            $badgeType = $registration['type'];
            $groupName = $registration['groupName'];
            $confirmationNumber = $registration['confirmationNumber'];
            $this->addBadge($regNumber, $regName, $badgeNumber, $badgeType, $groupName, $confirmationNumber);
        }

        return $this->pdf->Output("BulkBadge_{$type}_{$limit}_{$offset}.pdf", 'I');
    }

    /**
     * @param Registration $registration
     * @param Badge|null $badge
     * @return mixed
     */
    protected function printSingle($registration, $badge = null)
    {
        if ($badge) {
            $this->addBadgeFromRegistrationAndBadge($registration, $badge);
        } else {
            $badges = $registration->getBadges()->toArray();
            $badges = array_reverse($badges);
            /** @var $badges Badge[] */
            foreach ($badges as $badge) {
                if (!$badge->getBadgestatus()->getActive()) {
                    continue;
                }
                $this->addBadgeFromRegistrationAndBadge($registration, $badge);
            }
        }

        return $this->pdf->Output('Badge' . $registration->getNumber() . '.pdf', 'I');
    }

    /**
     * @param Registration $registration
     * @param Badge $badge
     */
    protected function addBadgeFromRegistrationAndBadge($registration, $badge)
    {
        $badgeType = $badge->getBadgetype();
        $registrationNumber = $registration->getNumber();
        $registrationName = $registration->getBadgeName();
        $badgeNumber = $badge->getNumber();
        $badgeType = $badgeType->getName();
        $confirmationNumber = $registration->getConfirmationNumber();
        $groups = $registration->getGroups();
        $groupName = '';
        foreach ($groups as $group) {
            if ($groupName) {
                $groupName .= ', ';
            }
            $groupName .= $group->getName();
        }
        $this->addBadge($registrationNumber, $registrationName, $badgeNumber, $badgeType, $groupName, $confirmationNumber);
    }

    /**
     * @param $regNumber String
     * @param $regName String
     * @param $badgeNumber String
     * @param $badgeType String
     * @param $groupName String
     * @param $confirmationNumber String
     */
    private function addBadge($regNumber, $regName, $badgeNumber, $badgeType, $groupName, $confirmationNumber)
    {
        $this->pdf->addPage();
        $event = $this->getDoctrine()->getRepository(Event::class)->getSelectedEvent();

        $imageFile = "images/badge_backgrounds/{$event->getYear()}/" . $badgeType . '.jpg';

        if ($this->pdf->getIsLandscape()) {
            $this->addBadgeFrontLandscape($regNumber, $regName, $badgeNumber, $groupName, $imageFile);
            $this->addBadgeBackLandscape($regNumber, $badgeNumber, $confirmationNumber);
        } else {
            $this->addBadgeFrontPortrait($regNumber, $regName, $badgeNumber, $groupName, $imageFile);
            $this->addBadgeBackPortrait($regNumber, $badgeNumber, $confirmationNumber);
        }
    }


    /**
     * @param $regNumber String
     * @param $regName String
     * @param $badgeNumber String
     * @param $groupName String
     * @param $imageFile String
     */
    private function addBadgeFrontLandscape($regNumber, $regName, $badgeNumber, $groupName, $imageFile) {
        $x = $this->pdf->GetPageWidth();
        $y = $this->pdf->getPageHeight();

        $this->pdf->Image($imageFile, -.25, -.275, $x + .25, $y + .275, '', '', '', false, 0, '', false, false, 0);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $this->pdf->SetFont('helveticaB', 'B', 13);
        $this->pdf->setXY(5.5, 7);
        $this->pdf->Cell(0, 0, $regNumber, 0, 0, 'L');
        $this->pdf->SetFont('helveticaB', 'B', 8);
        $this->pdf->setXY(12.5, 12.5);
        $this->pdf->Cell(0, 0, $badgeNumber, 0, 0, 'L');
        if ($groupName == '') {
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 11);
            $this->pdf->setXY(29, 6);
            $this->pdf->Cell(40, 13, $regName, 0, 0, 'C');
        } else {
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 10);
            $this->pdf->setXY(29, 5);
            $this->pdf->Cell(40, 13, $regName, 0, 0, 'C');
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 6.75);
            $this->pdf->setXY(29, 8);
            $this->pdf->Cell(40, 13, '(' . $groupName . ')', 0, 0, 'C');
        }
    }

    /**
     * @param $regNumber String
     * @param $regName String
     * @param $badgeNumber String
     * @param $groupName String
     * @param $imageFile String
     */
    private function addBadgeFrontPortrait($regNumber, $regName, $badgeNumber, $groupName, $imageFile) {
        $x = $this->pdf->GetPageWidth();
        $y = $this->pdf->getPageHeight();

        $this->pdf->Image($imageFile, -.25, -.275, $x + .25, $y + .275, '', '', '', false, 0, '', false, false, 0);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if ($groupName == '') {
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 11);
            $this->pdf->setXY(3, 68.5);
            $this->pdf->Cell(38, 11, $regNumber . '-' . $badgeNumber, 0, 0, 'C');
            $this->pdf->setXY(3, 73.5);
            $this->pdf->Cell(38, 11, $regName, 0, 0, 'C');
        } else {
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 10);
            $this->pdf->setXY(3, 68);
            $this->pdf->Cell(38, 11, $regNumber . '-' . $badgeNumber, 0, 0, 'C');
            $this->pdf->setXY(3, 71);
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 6.75);
            $this->pdf->Cell(38, 11, '(' . $groupName . ')', 0, 0, 'C');
            $this->pdf->setXY(3, 74);
            $this->pdf->SetFont('Bauhaus LT Medium', 'B', 10);
            $this->pdf->Cell(38, 11, $regName, 0, 0, 'C');
        }
    }

    /**
     * @param $regNumber String
     * @param $badgeNumber String
     * @param $confirmationNumber String
     */
    private function addBadgeBackLandscape($regNumber, $badgeNumber, $confirmationNumber)
    {
        $this->pdf->addPage();

        $img_file = 'images/ad_bw.jpg'; //3223 × 463
        $this->pdf->Image($img_file, 4.5, 6, 41.7667386, 6, '', '', '', true);

        $img_file = 'images/atc_bw.jpg';
        $this->pdf->Image($img_file, 62, 6, 17, 6.5, '', '', '', true);

        $this->pdf->SetFont('Bauhaus', 'B', 11);
        $this->pdf->setXY(4, 3);
        $this->pdf->Cell(77.6, 23, 'Disclaimers:', 0, 0, 'L');
        $this->pdf->SetFont('Bauhaus LT Medium', '', 5);
        $this->pdf->setXY(4, 17);
        $text = "Anime Detour staff reserve the right to remove any member from the convention at any time for any reason.";
        $this->pdf->MultiCell(77.6, 22, $text, 0, 'L');
        $this->pdf->setXY(4, 22);
        $text = "By being a member, you give your consent for your likeness to be used in any convention photos and"
            ." videos. (Not applicable to \"Guest\" badge holders.)";
        $this->pdf->MultiCell(77.6, 22, $text, 0, 'L');
        $this->pdf->setXY(4, 27);
        $text = "This badge is not a ticket. It represents your membership, and is for your use only. It may not be"
            ." sold, rented, or given to anyone else.";
        $this->pdf->MultiCell(77.6, 22, $text, 0, 'L');
        $this->pdf->setXY(4, 31.5);
        $text = "Badges may not be copied or otherwise reproducted in any form. Unauthorized duplication is a violation"
            ." of United States copyright law and carries severe criminal penalties.";
        $this->pdf->MultiCell(77.6, 22, $text, 0, 'L');

        /*
         Anime Detour staff reserve the right to remove any member from the convention at any time for any reason

         By being a member, you give your consent for your likeness to be used in any convention photos and videos. (Not applicable to "Guest" badge holders.)

         This badge is not a ticket. It represents your membership, and is for your use only. It may not be sold, rented, or given to anyone else.

         Badges may not be copied or otherwise reproducted in any form. Unauthorized duplication is a violation of United States copyright law and carries severe criminal penalties.
         */

        $this->pdf->SetFont('Bauhaus', 'B', 11);
        $this->pdf->setXY(4, 26);
        $this->pdf->Cell(77.6, 23, 'Harassment Policy:', 0, 0, 'L');
        $this->pdf->SetFont('Bauhaus LT Medium', '', 5);

        $this->pdf->setXY(4, 40);
        $text = "Anime Twin Cities, Inc. (ATC) is dedicated to providing harassment-free event experiences for"
            ." everyone. ATC’s full anti-harassment policy can be found at:"
            ." animetwincities.org/policyharrassment";
        $this->pdf->MultiCell(57, 22, $text, 0, 'L');

        $this->pdf->setXY(4, 48);
        $text = "© {$this->pdf->getEvent()->getYear()} Anime Twin Cities, Inc.";
        $this->pdf->MultiCell(77.6, 22, $text, 0, 'L');

        $style = array(
            'border' => 0,
            'vpadding' => false,
            'hpadding' => false,
            'fgcolor' => array(0,0,0),
            'bgcolor' => array(255,255,255),
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $this->pdf->setXY(3, 49);
        if ($confirmationNumber) {
            $confirmationNumber = substr($confirmationNumber, 0, 8);
            $year2digit = substr($this->pdf->getEvent()->getYear(), 2, 4);
            $this->pdf->write2DBarcode('$AD-B-'."$regNumber-$badgeNumber-$year2digit".$confirmationNumber,
                'QRCODE,Q', 60, 36, 40, 15, $style, 'L');
        }
    }

    private function addBadgeBackPortrait($regNumber, $badgeNumber, $confirmationNumber)
    {
        // TODO: Reformat to add barcode
        $this->pdf->addPage();

        $img_file = 'images/ad_bw.jpg'; //3223 × 463
        $this->pdf->Image($img_file, 6.5, 6, 41.7667386, 6, '', '', '', true);

        $img_file = 'images/atc_bw.jpg';
        $this->pdf->Image($img_file, 10, 46, 34, 13, '', '', '', true);

        $this->pdf->SetFont('Bauhaus', 'B', 11);
        $this->pdf->setXY(4, 3);
        $this->pdf->Cell(46, 23, 'Disclaimers:', 0, 0, 'C');
        $this->pdf->SetFont('Bauhaus LT Medium', '', 5);
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 22, 'Anime Detour staff reserve the right to', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 25, 'remove any member from the convention', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 28, 'at any time for any reason', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 14, '', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 34, 'By being a member, you give your consent for your', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 37, 'likeness to be used in any convention photos and videos.', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 40, '(Not applicable to "Guest" badge holders.)', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 43, '', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 46, 'This badge is not a ticket. It represents', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 49, 'your membership, and is for your use only. It', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 52, 'may not be sold, rented, or given to anyone else.', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 55, '', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 58, 'Badges may not be copied or otherwise', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 61, 'reproducted in any form. Unauthorized', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 64, 'duplication is a violation of United', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 67, 'States copyright law and carries severe', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 70, 'criminal penalties.', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 73, '', 0, 0, 'C');

        /*
         Anime Detour staff reserve the right to remove any member from the convention at any time for any reason

         By being a member, you give your consent for your likeness to be used in any convention photos and videos. (Not applicable to "Guest" badge holders.)

         This badge is not a ticket. It represents your membership, and is for your use only. It may not be sold, rented, or given to anyone else.

         Badges may not be copied or otherwise reproducted in any form. Unauthorized duplication is a violation of United States copyright law and carries severe criminal penalties.
         */

        $this->pdf->SetFont('Bauhaus', 'B', 11);
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 117, 'Harassment Policy:', 0, 0, 'C');
        $this->pdf->SetFont('Bauhaus LT Medium', '', 5);
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 123, 'Anime Twin Cities, Inc. (ATC) is dedicated to', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 126, 'providing harassment-free event experiences for', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 129, 'for everyone. ATC’s full anti-harassment policy', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 132, 'can be found at:', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 136, '', 0, 0, 'C');
        $this->pdf->setXY(4, 6);
        $this->pdf->Cell(46, 138, 'animetwincities.org/policyharrassment', 0, 0, 'C');
        //
    }
}
