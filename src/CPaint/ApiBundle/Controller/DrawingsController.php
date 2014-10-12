<?php

namespace CPaint\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;

class DrawingsController extends Controller
{
    public function getDrawingsAction()
    {
        $data = array('drawings' => []);

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
