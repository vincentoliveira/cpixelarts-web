<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Service\ColorService;

/**
 * @Route("/gallery")
 */
class GalleryController extends Controller
{

    /**
     * @Route("/", name="gallery_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');
        $query = $repo->findAllQuery();

        $paginator = $this->get('knp_paginator');
        $page = $this->get('request')->query->get('page', 1);
        $maxResults = 12;
        $drawings = $paginator->paginate($query, $page, $maxResults);

        return array(
            'drawings' => $drawings,
        );
    }

    /**
     * @Route("/drawing-{id}.gif", name="gallery_drawing")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     */
    public function bitmapAction(Drawing $drawing)
    {
        $colors = [];
        $width = $drawing->getWidth();
        $height = $drawing->getHeight();
        
        $bitmap = imagecreatetruecolor(2 * $width, 2 * $height);
        
        // set background to white
        $white = imagecolorallocate($bitmap, 255, 255, 255);
        imagefill($bitmap, 0, 0, $white);

        foreach ($drawing->getPixels() as $pixel) {
            $y = 2 * intval($pixel->getPosition() / $width);
            $x = 2 * ($pixel->getPosition() % $height);
            
            $color = $pixel->getColor();
            if (!isset($colors[$color])) {
                $rgb = ColorService::colorToRGB($color);
                $colors[$color] = imagecolorallocate($bitmap, $rgb['r'], $rgb['g'], $rgb['b']);
            }
            
            imagesetpixel($bitmap, $x, $y, $colors[$color]);
            imagesetpixel($bitmap, $x + 1, $y, $colors[$color]);
            imagesetpixel($bitmap, $x, $y + 1, $colors[$color]);
            imagesetpixel($bitmap, $x + 1, $y + 1, $colors[$color]);
        }
        $tmpfname = tempnam("/tmp", "cpaint");
        //imagepng($bitmap, $tmpfname);
        imagegif($bitmap, $tmpfname);
        $bitmapContent = file_get_contents($tmpfname);

        $response = new Response($bitmapContent);
        $response->headers->set('Content-Type', 'image/png');

        unlink($tmpfname);
        
        return $response;
    }

}
