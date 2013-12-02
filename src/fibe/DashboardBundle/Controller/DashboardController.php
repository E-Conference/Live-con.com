<?php

namespace fibe\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * @Route("/" , name="dashboard_choose_conference")
     * @Template()
     */
    public function chooseConferenceAction()
    {
        $currentUser = $this->getUser();

        return array(
        	'entity' => $currentUser
        	);
    }

      /**
     * @Route("/" , name="dashboard_enter_conference")
     */
    public function enterConferenceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $choosenConf = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);
        $user = $this->getUser();
        $user->setCurrentConf($choosenConf);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('dashboard_index'));
    
    }



}
