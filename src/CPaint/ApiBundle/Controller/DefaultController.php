<?php

namespace CPaint\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;

class DefaultController extends Controller
{
    /**
     * @Route("/version.{_format}", defaults={ "_format" = "json" })
     */
    public function getVersionAction()
    {
        $version = $this->container->getParameter('api_version');
        $data = array('version' => $version);

        $view = View::create()
            ->setData($data);

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
