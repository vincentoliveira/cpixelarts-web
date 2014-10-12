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
    /**
     * @Route("/", name="draw_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');
        $query = $repo->findAllQuery();

        $paginator = $this->get('knp_paginator');
        $page = $this->get('request')->query->get('page', 1);
        $maxResults = 7;
        $drawings = $paginator->paginate($query, $page, $maxResults);

        return array(
            'drawings' => $drawings,
        );
    }
    
    /**
     * @Route("/new", name="draw_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(new DrawingType());
        if ($request->isMethod("POST")) {
            $form->submit($request);
            
            if ($form->isValid()) {
                $drawing = $form->getData();
                $drawing->setHeight($drawing->getWidth());        
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($drawing);
                $em->flush();
            
                return $this->redirect($this->generateUrl('draw_edit', array('id' => $drawing->getId())));
            }
        }
        
        return array(
            'form' => $form->createView(),
        );
    }
    

    /**
     * @Route("/{id}/edit", name="draw_edit")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Template()
     */
    public function editAction(Drawing $drawing)
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
        
        return array(
            'drawing' => $drawing,
            'pixels' => $pixels,
            'colors' => $colors,
            'rgbColors' => ColorService::RGBStringColors(),
        );
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
        
        if ($color !== null && $position !== null) {
            $pixel = new Pixel();
            $pixel->setColor($color);
            $pixel->setDrawing($drawing);
            $pixel->setPosition($position);

            $em = $this->getDoctrine()->getManager();
            $em->persist($pixel);
            $em->flush();
        } else {
            // print error
            $session = $this->container->get('session');
            $session->getFlashBag()->add('error', 'Failed to add this pixel');
        }
            
        return $this->redirect($this->generateUrl('draw_edit', array('id' => $drawing->getId())));
    }
}
