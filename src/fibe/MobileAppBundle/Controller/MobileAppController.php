<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Mobile app controller.
 *
 * @Route("/MobileApplication")
 */
class MobileAppController extends Controller
{
    /**
     * @Route("/",name="mobileApp_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
