<?php

namespace fibe\HomePageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



/**
 * Homepage Controller 
 *
 * @Route("/")
 */
class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
