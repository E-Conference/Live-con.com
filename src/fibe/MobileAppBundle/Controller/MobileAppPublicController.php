<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Mobile app controller.
 *
 * @Route("/MobileApplicationPublic")
 */
class MobileAppPublicController extends Controller
{
    /**
     * @Route("/{id}",name="mobileAppPublic_index")
     * @Template()
     */
    public function indexAction($id)
    {
        return array();
    }
}
