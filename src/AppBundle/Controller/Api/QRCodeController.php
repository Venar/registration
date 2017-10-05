<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PHPQRCode\QRcode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QRCodeController extends Controller
{
    /**
     * @Route("/api/barcode/{text}", name="api_barcode_generate")
     *
     * @param String $text Text to generate as a QRCode
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
