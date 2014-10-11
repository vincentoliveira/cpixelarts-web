<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CPaint\DrawingBundle\Entity\Drawing;

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
     * @Route("/{id}/details", name="gallery_details")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Template()
     */
    public function detailsAction(Drawing $drawing)
    {
        return array(
            'drawing' => $drawing,
        );
    }

    /**
     * @Route("/{id}/bitmap", name="gallery_bitmap")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     */
    public function bitmapAction(Drawing $drawing)
    {
        $colors = [];
        $width = $drawing->getWidth();
        $height = $drawing->getHeight();
        
        $bitmap = imagecreatetruecolor($drawing->getWidth(), $drawing->getHeight());
        foreach ($drawing->getPixels() as $pixel) {
            $y = intval($pixel->getPosition() / $width);
            $x = $pixel->getPosition() % $height;
            
            if (!isset($colors[$pixel->getColor()])) {
                $color = $pixel->getColor();
                $r =  $color & 0xb0;
                $g = $color & 0x1b << 4;
                $b = ($color & 0x03) << 6;
                
                $colors[$color] = imagecolorallocate($bitmap, $r, $g, $b);
            }
            $color = $colors[$pixel->getColor()];
            
            imagesetpixel($bitmap, $x, $y, $color);
        }
        $tmpfname = tempnam("/tmp", "cpaint");
        imagepng($bitmap, $tmpfname);
        $bitmapContent = file_get_contents($tmpfname);

        $response = new Response($bitmapContent);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

}
