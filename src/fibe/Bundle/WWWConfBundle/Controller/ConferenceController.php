<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Form\WwwConfType;

use IDCI\Bundle\SimpleScheduleBundle\Form\XPropertyType; 
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Event; 


/**
 * Link controller.
 *
 * @Route("/admin/conference")
 */
class ConferenceController extends Controller
{
/**
 * @Route("/edit", name="wwwconf_conference_edit")
 * @Template()
 */
    public function editAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();       
  	  $confManager = $this->get('security.context')->getToken()->getUser();
      $wwwConf = $confManager->getWwwConf();
      if(!$wwwConf)
        $wwwConf = new WwwConf();
      $form = $this->createForm(new WwwConfType(), $wwwConf);
      
      $request = $this->get('request');
      if ($request->getMethod() == 'POST') {
        $form->bind($request);
     
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager(); 
            $confManager->setWwwConf($wwwConf);
            $em->persist($wwwConf);
            $em->flush();

            $this->container->get('session')->getFlashBag()->add(
                'success',
                'The conference has been successfully updated'
            );
        }else{

            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Submition error, please try again.'
            );
        }
      } 
      return array(
          'wwwConf' => $wwwConf,
          'form' => $form->createView()
      );
    }
      
}
