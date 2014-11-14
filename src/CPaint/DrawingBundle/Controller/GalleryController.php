<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Route("/drawing-{slug}_{width}x{height}.{ext}", name="gallery_drawing_width_height")
     * @Route("/drawing-{slug}_{width}.{ext}", name="gallery_drawing_width")
     * @Route("/drawing-{slug}.{ext}", name="gallery_drawing")
     * @Route("/drawing-{slug}", name="gallery_drawing_no_ext")
     */
    public function exportAction($slug, $ext = 'gif', $width = 0, $height = 0)
    {
        $drawing = $this->findDrawingBySlug($slug);
        if ($drawing === null) {
            return $this->createNotFoundException();
        }
        
        if ($width > 0 && $height <= 0) {
            // if heigh not set, calculate height proportionally
            $height = $drawing->getHeight() * $width / $drawing->getWidth();
        }
        
        $service = $this->get('cpaint.export');
        if (strtolower($ext) === 'png') {
            $ext = 'png';
            $filename = $service->exportPng($drawing, $width, $height);
        } else {
            $ext = 'gif';
            $filename = $service->exportGif($drawing, $width, $height);
        }
        
        $pixelArtContent = file_get_contents($filename);

        $response = new Response($pixelArtContent);
        $response->headers->set('Content-Type', 'image/' . $ext);

        unlink($filename);
        
        return $response;
    }
    
    /**
     * Find drawing by slug or ID
     * 
     * @param String $slug Slug or ID
     * @return Drawing
     */
    private function findDrawingBySlug($slug) 
    {
        $repo = $this->getDoctrine()->getRepository("CPaintDrawingBundle:Drawing");
        $drawing = $repo->findOneByTitleCanonical($slug);
        if ($drawing === null) {
            $drawing = $repo->find($slug);
        }
        
        return $drawing;
    }

}
