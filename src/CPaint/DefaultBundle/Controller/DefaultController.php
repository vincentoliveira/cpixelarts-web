<?php

namespace CPaint\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Service\ColorService;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $drawing = new Drawing();
        $drawing->setCreatedAt(new \DateTime());
        $drawing->setHeight(16);
        $drawing->setWidth(16);
        
        $colors = [];
        $pixels = array_fill(0, $drawing->getWidth() * $drawing->getHeight(), false);
        
        return array(
            'drawing' => $drawing,
            'pixels' => $pixels,
            'colors' => $colors,
            'currentColor' => rand(0, 255),
            'rgbColors' => ColorService::RGBStringColors(),
        );
    }
}
