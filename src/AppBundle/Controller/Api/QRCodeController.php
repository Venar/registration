<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PHPQRCode\QRcode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QRCodeController extends Controller
{
    /**
     * @Route("/api/barcode/{text}")
     */
    public function getQRCode($text)
    {
        if (substr($text, 0, 4) !== '$AD-') {
            $text = '$AD-C-'.$text;
        }
        QRcode::png($text);
        die();
    }
}
