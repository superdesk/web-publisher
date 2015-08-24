<?php

namespace SWP\WebRendererBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Cache(expires="+ 5 min", public=true)
     */
    public function indexAction()
    {
        return $this->render('views/index.html.twig');
    }
}
