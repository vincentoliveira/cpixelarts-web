<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
        return array();
    }
}
