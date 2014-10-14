<?php

namespace CPaint\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CPaintDrawingBundle:Drawing');
        $drawing = $repo->find($id);

        if (null === $drawing) {
            throw new NotFoundHttpException(sprintf("Drawing with id '%s' could not be found.", $id));
        }
        
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
}
