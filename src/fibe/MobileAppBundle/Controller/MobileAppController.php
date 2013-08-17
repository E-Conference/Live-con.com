<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MobileAppController extends Controller
{
    /**
     * @Route("/MobileApplication") name="mobileApp_index"
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
