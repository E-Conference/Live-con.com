<?php

namespace fibe\ExternalizationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Exporter controller.
 * @Route("/exporter")
 */
class ExportController extends Controller
{

	/**
     * Exporter index.
    *
    * @Route("/", name="externalization_export_index")
    * @Template()
    */
    public function indexAction()
    {
      
        $em = $this->getDoctrine()->getManager();       
        $wwwConf = $this->getUser()->getCurrentConf();

        $export_form = $this->createFormBuilder()
        ->add('export_format', 'choice',array( 
                               'choices'   => array('xml' => 'SWC'),
                               'required'  => true,)
        )
        ->getForm();

        //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        return array(
              'wwwConf'  => $wwwConf,
              'authorized' => $authorization->getFlagconfDatas(),
              'export_form' => $export_form->createView()
              );
    }


  /**
    * Exporter process.
    *
    * @Route("/process", name="externalization_export_process")
    * 
    * 
    */
    public function processAction(Request $request)
    {
      
        $export_form = $this->createFormBuilder()
        ->add('export_format', 'choice',array( 
                               'choices'   => array('xml' => 'SWC'),
                               'required'  => true,)
        )
        ->getForm();
        $export_form->bind($request);


        $format = $export_form["export_format"]->getData();

        $wwwConf = $this->getUser()->getCurrentConf();
        $conferences =  new \Doctrine\Common\Collections\ArrayCollection();
        $conferences->add($wwwConf);

        $export = $this->get('idci_exporter.manager')->export($conferences, $format);
        $filename= $wwwConf->getConfName().".".$format;

        $response = new Response($export->getContent());
        $response->headers->set('Content-Type', 'text/'.$format);
        $response->headers->set('Content-Disposition', 'attachment;filename='.$filename);

       
        return $response;
    }


}
