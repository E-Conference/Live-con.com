<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Form\MobileAppConfigType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mobile app controller.
 *
 * @Route("app/settings")
 */
class MobileAppSettingsController extends Controller
{


	/**
     * @Route("/",name="mobileAppSettings_index")
     * @Template()
     */
    public function indexAction()
    {

         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
       
        $mobile_app_config =$user->getCurrentConf()->getAppConfig();
        $mobile_app_form = $this->createForm(new MobileAppConfigType(), $mobile_app_config);

		return $this->render('fibeMobileAppBundle:MobileAppSettings:index.html.twig', array(
            'mobile_app_form' => $mobile_app_form->createView(),
		    'mobile_app_config' => $mobile_app_config,
            'authorized' => $authorization->getFlagApp()
		    
		));
	}


	 /**
     * @Route("/{id}/update/setting",name="mobileAppTheme_update_settings")
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        //Authorization Verification conference app manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagApp()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:MobileAppConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find mobile app config entity.');
        }

        $editForm = $this->createForm(new MobileAppConfigType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('mobileAppSettings_index'));
    }




}
