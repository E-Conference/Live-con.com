<?php

namespace fibe\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
class FrontController extends Controller
{
    /**
     * @Route("/angular")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
