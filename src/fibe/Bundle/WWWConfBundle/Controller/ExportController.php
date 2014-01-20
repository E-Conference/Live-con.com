<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Equipment controller.
 * @Route("/exporter")
 */
class ExportController extends Controller
{

	/**
     * Exporter index.
    *
    * @Route("/", name="schedule_export_index")
    * @Template()
    */
    public function indexAction()
    {
      
        $em = $this->getDoctrine()->getManager();       
        $wwwConf = $this->getUser()->getCurrentConf();

        //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        return array(
              'wwwConf'  => $wwwConf,
              'authorized' => $authorization->getFlagconfDatas()
              );
    }


}
