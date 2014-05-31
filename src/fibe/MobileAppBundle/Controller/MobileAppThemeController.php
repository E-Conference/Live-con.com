<?php

namespace fibe\MobileAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\MobileAppBundle\Entity\MobileAppConfig;
use fibe\MobileAppBundle\Form\MobileAppWwwConfType;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Form\WwwConfType;
use fibe\Bundle\WWWConfBundle\Form\MobileAppConfigType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Mobile app controller.
 *
 * @Route("/app/theme")
 */
class MobileAppThemeController extends Controller
{


    /**
     * @Route("/",name="mobileAppTheme_index")
     * @Template()
     */
    public function indexAction()
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
     

    $granted = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'WwwConf');
    $user = $this->getUser();


    $mobile_app_config = $user->getCurrentConf()->getAppConfig();
    $mobile_app_form = $this->createForm(new MobileAppConfigType(), $mobile_app_config);

    $conference = $user->getCurrentConf();

    return $this->render(
      'fibeMobileAppBundle:MobileAppTheme:index.html.twig',
      array(
        'mobile_app_form' => $mobile_app_form->createView(),
        'mobile_app_config' => $mobile_app_config,
        'conference' => $conference,
        'authorized' => isset($granted)
      )
    );
  }


  /**
   * @Route("/{id}/update/style",name="mobileAppTheme_update_style")
   * @Template()
   */
  public function updateMobileAppAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'MobileAppConfig', $id);
    $em = $this->getDoctrine()->getManager();

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find mobile app config entity.');
    }

    $editForm = $this->createForm(new MobileAppConfigType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em->persist($entity);
      $em->flush();
      // $this->clearCache();
    }

    return $this->redirect($this->generateUrl('mobileAppTheme_index'));
  }


  public function clearCache()
  {
    $fileCache = $this->container->get('twig')->getCacheFilename('fibeMobileAppBundle:MobileAppPublic:index.html.twig');

    if (is_file($fileCache))
    {
      @unlink($fileCache);
    }
  }

}
