<?php

namespace AppBundle\Controller\Backend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProtectedImageController extends Controller
{
    /**
     * @Route("/uploads/images/badges/{fileName}", name="viewProtectedImage")
     * @Security("has_role('ROLE_SUBHEAD')")
     *
     * @param Request $request
     * @param string $fileName
     * @return BinaryFileResponse
     */
    public function showImage(Request $request, $fileName)
    {
        $filePath = $this->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR
            . 'images' . DIRECTORY_SEPARATOR
            . 'badges' . DIRECTORY_SEPARATOR
            . $fileName;
        ;

        $response = new BinaryFileResponse($filePath);
        // you can modify headers here, before returning
        return $response;
    }
}
