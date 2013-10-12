<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

use fibe\Bundle\WWWConfBundle\Form\WwwConfType;
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


        $mobile_app_config = $this->getUser()->getCurrentConf()->getAppConfig();
        $mobile_app_form = $this->createForm(new MobileAppConfigType(), $mobile_app_config);

        $conference = $this->getUser()->getCurrentConf();
        $conference_form = $this->createForm(new WwwConfType(), $conference);



		return array(
		    'mobile_app_config' => $mobile_app_config,
            'conference' => $conference,
		    'mobile_app_form' => $mobile_app_form->createView(),
            'conference_form' => $conference_form->createView(),
		);
	}

     /**
     * @Route("/{id}/update",name="mobileAppTheme_update")
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {


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
        return $this->redirect($this->generateUrl('mobileAppTheme_index'));
    }

}
