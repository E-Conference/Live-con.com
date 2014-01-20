<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Equipment controller.
 * @Route("/externalization")
 */
class ExternalizationController extends Controller
{

	/**
     * Importer access.
    *
    * @Route("/importer", name="schedule_externalization_importer")
    * @Template()
    */
    public function importAction()
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



    /**
     * Exporter access
    *
    * @Route("/exporter", name="schedule_externalization_exporter")
    * @Template()
    */
    public function exportAction()
    {
       return  array();
    }
}
