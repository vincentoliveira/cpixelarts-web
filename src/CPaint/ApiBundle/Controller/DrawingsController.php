<?php

namespace CPaint\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CPaint\DrawingBundle\Entity\Drawing;
use FOS\RestBundle\View\View;

class DrawingsController extends Controller
{

    /**
     * Get paginated drawing list.
     * 
     * Optional parameters:
     * - page (default: 1)
     * - max_results (default: 10)
     * 
     * @api
     */
    public function getDrawingsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');

        $page = $this->get('request')->query->get('page', 1);
        $maxResults = $this->get('request')->query->get('max_results', 10);
        $data = $repo->findAllPaginated($page, $maxResults);

        $view = View::create()
                ->setData($data);

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Get drawing by id
     * 
     * @api
     */
    public function getDrawingAction($id)
    {
        $drawing = $this->getDrawing($id);
        $view = View::create()
                ->setData(array('drawing' => $drawing));

        return $this->getViewHandler()->handle($view);
    }
    
    /**
     * Create a new drawing
     * 
     * @api
     */
    public function postDrawingsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $width = $request->request->get('width', -1);
        $height = $request->request->get('height', -1);
        $title = $request->request->get('title', null);
        
        $service = $this->get('cpaint.drawing');
        $drawing = $service->initDrawing($width, $height, $title);
        
        // add a pixel
        $color = intval($request->request->get('color', -1));
        $position = intval($request->request->get('position', -1));
        $pixel = $service->addPixelToDrawing($drawing, $color, $position);
        if ($pixel !== null) {
            $em->persist($pixel);
            $drawing->addPixel($pixel);
        }
        
        $em->persist($drawing);
        $em->flush();
        
        $view = View::create()
                ->setData(array('drawing' => $drawing));

        return $this->getViewHandler()->handle($view);
    }
    
    /**
     * Set drawing title
     * 
     * @api
     */
    public function titleDrawingAction(Request $request, $id)
    {
        $drawing = $this->getDrawing($id);
        $title = $request->request->get('title');
        if ($title === null) {
            throw new HttpException(400, "The title cannot be null");
        }
        
        $service = $this->get('cpaint.drawing');
        $titleCanonical = $service->canonicalizeTitle($title);
        $drawing->setTitle($title);
        $drawing->setTitleCanonical($titleCanonical);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($drawing);
        $em->flush();
        
        $view = View::create()
                ->setData(array('drawing' => $drawing));

        return $this->getViewHandler()->handle($view);
    }
    /**
     * Lock drawing
     * 
     * @api
     */
    public function lockDrawingAction($id)
    {
        $service = $this->get('cpaint.drawing');
        
        $drawing = $this->getDrawing($id);
        $drawing->setLocked(true);
        $drawing->setDisplayable($service->isDisplayable($drawing));

        
        $em = $this->getDoctrine()->getManager();
        $em->persist($drawing);
        $em->flush();
        
        $view = View::create()
                ->setData(array('drawing' => $drawing));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Add a pixel to a drawing
     * 
     * @api
     */
    public function postDrawingPixelsAction(Request $request, $id)
    {
        $drawing = $this->getDrawing($id);
        
        $color = intval($request->request->get('color', -1));
        $position = intval($request->request->get('position', -1));

        $service = $this->get('cpaint.drawing');
        $pixel = $service->addPixelToDrawing($drawing, $color, $position);
        if ($pixel === null) {
            throw new HttpException(400, sprintf("This pixel (%d, %d) is not valid", $color, $position));
        }
        
        $drawing->addPixel($pixel);
        $drawing->setDisplayable($service->isDisplayable($drawing));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($pixel);
        $em->persist($drawing);
        $em->flush();
        
        $view = View::create()
                ->setData(array('drawing' => $drawing));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }

    /**
     * Get drawing by id
     * 
     * @param int $id
     * @return \CPaint\DrawingBundle\Entity\Drawing
     * @throws NotFoundHttpException
     */
    protected function getDrawing($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');
        $drawing = $repo->find($id);

        if (null === $drawing) {
            throw new NotFoundHttpException(sprintf("Drawing with id '%s' could not be found.", $id));
        }

        return $drawing;
    }

}
