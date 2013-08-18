<?php

namespace fibe\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Dashboard Controller 
 *
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/" , name="dashboard_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
