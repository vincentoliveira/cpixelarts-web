<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Service\ColorService;
use CPaint\DrawingBundle\Service\DrawingService;

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
     * @Route("/drawing-{id}_{width}x{height}.gif", name="gallery_drawing_width_height")
     * @Route("/drawing-{id}_{width}.gif", name="gallery_drawing_width")
     * @Route("/drawing-{id}.gif", name="gallery_drawing")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     */
    public function bitmapAction(Drawing $drawing, $width = 0, $height = 0)
    {
        if ($width > 0 && $height <= 0) {
            // if heigh not set, calculate height proportionally
            $height = $drawing->getHeight() * $width / $drawing->getWidth();
        }
        
        $service = new DrawingService();
        $filename = $service->exportGif($drawing, $width, $height);
        $bitmapContent = file_get_contents($filename);

        $response = new Response($bitmapContent);
        $response->headers->set('Content-Type', 'image/png');

        unlink($filename);
        
        return $response;
    }

}
