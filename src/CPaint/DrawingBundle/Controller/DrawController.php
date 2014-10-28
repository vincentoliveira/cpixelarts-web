<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Entity\Pixel;
use CPaint\DrawingBundle\Form\DrawingType;
use CPaint\DrawingBundle\Service\ColorService;

/**
 * Draw Controller
 * 
 * @Route("/draw")
 */
class DrawController extends Controller
{

//    /**
//     * @Route("/", name="draw_index")
//     * @Template()
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');
//        $query = $repo->findAllQuery();
//
//        $paginator = $this->get('knp_paginator');
//        $page = $this->get('request')->query->get('page', 1);
//        $maxResults = 7;
//        $drawings = $paginator->paginate($query, $page, $maxResults);
//
//        return array(
//            'drawings' => $drawings,
//        );
//    }
//    
    /**
     * @Route("/new", name="draw_new")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $width = $request->request->get('width', -1);
        $height = $request->request->get('height', -1);
        $color = $request->request->get('color', -1);
        $position = $request->request->get('position', -1);

        if ($width < 0 || $height < 0 || $color < 0 || $position < 0) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $drawing = new Drawing();
        $drawing->setCreatedAt(new \DateTime());
        $drawing->setHeight($height);
        $drawing->setWidth($width);
        
        $pixel = $this->addPixelToDrawing($drawing, $color, $position);
        if ($pixel !== null) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pixel);
            $em->persist($drawing);
            $em->flush();
        } else {
            // print error
            $session = $this->container->get('session');
            $session->getFlashBag()->add('error', 'Failed to add this pixel');
        }

        return $this->redirect($this->generateUrl('draw_edit', array('id' => $drawing->getId(), 'color' => $color)));
    }

    /**
     * @Route("/{id}/edit", name="draw_edit")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Template()
     */
    public function editAction(Request $request, Drawing $drawing)
    {
        $colors = [];
        $pixels = array_fill(0, $drawing->getWidth() * $drawing->getHeight(), false);
        foreach ($drawing->getPixels() as $pixel) {
            $color = $pixel->getColor();

            if (!isset($colors[$color])) {
                $colors[$color] = $rgb = ColorService::colorToRGBString($color);
            }

            $pixels[$pixel->getPosition()] = $colors[$color];
        }

        $currentColor = $request->query->get('color', rand(0, 255));

        return array(
            'drawing' => $drawing,
            'pixels' => $pixels,
            'colors' => $colors,
            'currentColor' => $currentColor,
            'rgbColors' => ColorService::RGBStringColors(),
        );
    }

    /**
     * @Route("/{id}/set_title", name="draw_set_title")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Method("POST")
     */
    public function setTitleAction(Request $request, Drawing $drawing)
    {
        $title = $request->request->get('title', null);
        
        if ($title) {
            $drawing->setTitle($title);
            $em = $this->getDoctrine()->getManager();
            $em->persist($drawing);
            $em->flush();
        }
            
        return $this->redirect($this->generateUrl('draw_edit', array('id' => $drawing->getId())));
    }

    /**
     * @Route("/{id}/add_pixel", name="draw_add_pixel")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Method("POST")
     */
    public function addPixelAction(Request $request, Drawing $drawing)
    {
        $color = $request->request->get('color');
        $position = $request->request->get('position');

        $pixel = $this->addPixelToDrawing($drawing, $color, $position);
        if ($pixel !== null) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pixel);
            $em->flush();
        } else {
            // print error
            $session = $this->container->get('session');
            $session->getFlashBag()->add('error', 'Failed to add this pixel');
        }

        return $this->redirect($this->generateUrl('draw_edit', array('id' => $drawing->getId(), 'color' => $color)));
    }

    /**
     * Add pixel to a drawing. Return null on error
     * 
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @param type $color
     * @param type $position
     * @return \CPaint\DrawingBundle\Entity\Pixel|null
     */
    private function addPixelToDrawing(Drawing $drawing, $color, $position)
    {
        if ($drawing === null ||  $color < 0 || $color >= 256 || $position < 0 || 
                $position >= $drawing->getWidth() * $drawing->getHeight()) {
            return null;
        }
        
        $pixel = new Pixel();
        $pixel->setColor($color);
        $pixel->setDrawing($drawing);
        $pixel->setPosition($position);
        
        return $pixel;
    }

}
