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
use CPaint\DrawingBundle\Service\ColorService;
use CPaint\DrawingBundle\Service\DrawingService;

/**
 * Drawing Controller
 * 
 * @Route("/drawing")
 */
class DrawingController extends Controller
{

    /**
     * @Route("/new_{width}x{height}", name="drawing_new")
     * @Template()
     */
    public function newAction(Request $request, $width = 0, $height = 0)
    {
        $service = $this->get('cpaint.drawing');
        $drawing = $service->initDrawing($width, $height);

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

    /**
     * @Route("/create", name="drawing_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $width = $request->request->get('width', -1);
        $height = $request->request->get('height', -1);
        $color = $request->request->get('color', -1);
        $position = $request->request->get('position', -1);

        // create drawing
        $service = $this->get('cpaint.drawing');
        $drawing = $service->initDrawing($width, $height);
        $em->persist($drawing);

        // try to add pixel
        $pixels = $service->addPixelsToDrawing($drawing, $color, $position);
        foreach ($pixels as $pixel) {
            $em->persist($pixel);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('drawing_edit', array('id' => $drawing->getId(), 'color' => $color)));
    }

    /**
     * @Route("/{id}/edit", name="drawing_edit")
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
            'currentColor' => $currentColor % 256,
            'rgbColors' => ColorService::RGBStringColors(),
        );
    }

    /**
     * @Route("/{id}/set_title", name="drawing_set_title")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Method("POST")
     */
    public function setTitleAction(Request $request, Drawing $drawing)
    {
        $title = $request->request->get('title', null);

        if ($title) {
            $service = $this->get('cpaint.drawing');
            $titleCanonical = $service->canonicalizeTitle($title);
            $drawing->setTitle($title);
            $drawing->setTitleCanonical($titleCanonical);
            $em = $this->getDoctrine()->getManager();
            $em->persist($drawing);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('drawing_edit', array('id' => $drawing->getId())));
    }

    /**
     * @Route("/{id}/add_pixel", name="drawing_add_pixel")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     * @Method("POST")
     */
    public function addPixelAction(Request $request, Drawing $drawing)
    {
        $em = $this->getDoctrine()->getManager();
        
        $color = $request->request->get('color', -1);
        $position = $request->request->get('position', -1);

        $service = $this->get('cpaint.drawing');
        $pixels = $service->addPixelsToDrawing($drawing, $color, $position);
        if (empty($pixels)) {
            // print error
            $session = $this->container->get('session');
            $session->getFlashBag()->add('error', 'Failed to add this pixel');
        } else {
            foreach ($pixels as $pixel) {
                $em->persist($pixel);
            }        

            $drawing->setDisplayable($service->isDisplayable($drawing));
        }

        return $this->redirect($this->generateUrl('drawing_edit', array('id' => $drawing->getId(), 'color' => $color)));
    }

    /**
     * @Route("/{id}/lock", name="drawing_lock")
     * @ParamConverter("drawing", class="CPaintDrawingBundle:Drawing")
     */
    public function lockAction(Request $request, Drawing $drawing)
    {
        $service = $this->get('cpaint.drawing');
        $drawing->setLocked(true);
        $drawing->setDisplayable($service->isDisplayable($drawing));

        $em = $this->getDoctrine()->getManager();
        $em->persist($drawing);
        $em->flush();

        return $this->redirect($this->generateUrl('drawing_edit', array('id' => $drawing->getId())));
    }

    /**
     * @Route("/{slug}", name="drawing_show")
     */
    public function showAction($slug)
    {
        $repo = $this->getDoctrine()->getRepository("CPaintDrawingBundle:Drawing");
        $drawing = $repo->findOneByTitleCanonical($slug);
        if ($drawing === null) {
            $drawing = $repo->find($slug);
            if ($drawing === null) {
                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        return $this->forward('CPaintDrawingBundle:Drawing:edit', array(
                    'drawing' => $drawing,
        ));
    }

}
