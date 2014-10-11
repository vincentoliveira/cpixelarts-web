<?php

namespace CPaint\DrawingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
        return array();
    }
}
