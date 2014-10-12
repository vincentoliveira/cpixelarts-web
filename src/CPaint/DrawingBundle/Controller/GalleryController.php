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
     * @Route("/{id}/details", name="gallery_details")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Template()
     */
    public function detailsAction(Drawing $drawing)
    {
        $colors = [];
        foreach ($drawing->getPixels() as $pixel) {
            $color = $pixel->getColor();
            $rgb = ColorService::colorToRGB($color);
            $colors[$color] = sprintf("%02x%02x%02x", $rgb['r'], $rgb['g'], $rgb['b']);
        }
        
        return array(
            'drawing' => $drawing,
            'colors' => $colors,
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
                $rgb = ColorService::colorToRGB($color);
                $colors[$color] = imagecolorallocate($bitmap, $rgb['r'], $rgb['g'], $rgb['b']);
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
