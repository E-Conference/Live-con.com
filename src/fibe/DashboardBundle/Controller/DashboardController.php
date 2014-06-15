<?php

namespace fibe\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Dashboard Controller
 *
 * @Route("/Dashboard")
 */
class DashboardController extends Controller
{
  /**
   * @Route("/" , name="dashboard_index")
   * @Template()
   */
  public function indexAction()
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'WwwConf');

    return array('conferences' => $entities);
  }


  /**
   * @Route("{id}/enter" , name="dashboard_enter_conference")
   */
  public function enterConferenceAction($id)
  {

    $em = $this->getDoctrine()->getManager();
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'WwwConf', $id);

    $user = $this->getUser();
    $user->setCurrentConf($entity);
    $em->persist($user);
    $em->flush();

    return $this->redirect($this->generateUrl('schedule_conference_show'));

  }


}
