<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
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
    	$em = $this->getDoctrine()->getManager();
    	$conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);
    	$mobile_app_config = $conference->getAppConfig();
        
		return array(
		    'mobile_app_config' => $mobile_app_config,
            'conference' => $conference,
        );
    }
}
