<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig;
use fibe\Bundle\WWWConfBundle\Form\MobileAppConfigType;
/**
 * Mobile app controller.
 *
 * @Route("/MobileApplicationTheme")
 */
class MobileAppThemeController extends Controller
{
    /**
     * @Route("/",name="mobileAppTheme_index")
     * @Template()
     */
    public function indexAction()
    {



    	/*$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);
 


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        
        $this->get('session')->getFlashBag()->add(
            'info',
            $this->get('translator')->trans('%entity%[%id%] has been updated', array(
                '%entity%' => 'Event',
                '%id%'     => $entity->getId()
            ))
        );
            
        $form = $this->createForm(new EventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);*/

        $mobile_app_config = new MobileAppConfig();
        $mobile_app_form = $this->createForm(new MobileAppConfigType(), $mobile_app_config);


		$WwwConf="";

			return array(
			    'entity' => $mobile_app_config,
			    "mobile_app_form" => $mobile_app_form->createView(),

			);

		
		}

     /**
     * @Route("/update",name="mobileAppTheme_update")
     * @Template()
     */
    public function updateAction()
    {



        /*$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);
 


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        
        $this->get('session')->getFlashBag()->add(
            'info',
            $this->get('translator')->trans('%entity%[%id%] has been updated', array(
                '%entity%' => 'Event',
                '%id%'     => $entity->getId()
            ))
        );
            
        $form = $this->createForm(new EventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);*/

        $mobile_app_config = new MobileAppConfig();
        $mobile_app_form = $this->createForm(new MobileAppConfigType(), $mobile_app_config);


        $WwwConf="";

            return array(
                'entity' => $mobile_app_config,
                "mobile_app_form" => $mobile_app_form->createView(),

            );

        
        }
}
