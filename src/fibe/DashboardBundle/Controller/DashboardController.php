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
      return array();
    }

    

      /**
     * @Route("{id}/enter" , name="dashboard_enter_conference")
     */
    public function enterConferenceAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $choosenConf = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);
        $user=$this->getUser();
        if (!$user->authorizedAccesToConference($choosenConf)) {
          throw new AccessDeniedException('Look at your conferences !!!');
        } 
        
        $user = $this->getUser();
        $user->setCurrentConf($choosenConf);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('schedule_conference_edit'));
    
    }



}
