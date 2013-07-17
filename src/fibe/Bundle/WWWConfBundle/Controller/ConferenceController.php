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
 * @Route("/admin/manage-conference")
 */
class ConferenceController extends Controller
{
/**
 * @Route("/show", name="wwwconf_conference_show")
 * @Template()
 */
    public function showAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();       
	  $confManager = $this->get('security.context')->getToken()->getUser();
	  $wwwConf = new WwwConf();
      $form = $this->createForm(new WwwConfType(), $wwwConf);
      
      $request = $this->get('request');
      if ($request->getMethod() == 'POST') {
        $form->bind($request);
     
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $wwwConf->setConfManager($confManager);
            $em->persist($wwwConf);
            $em->flush();

            $response = new Response(json_encode($wwwConf->getId()));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
      } 
      return array('confManager'     => $confManager,
                   'confManagerForm' => $form->createView());
    }
     
    
/**
 * @Route("/delete-{wwwConfId}", name="wwwconf_admin_delete_conf")
 */
  
      
    public function deleteConfAction(Request $request,$wwwConfId)
    {
        $em = $this->getDoctrine()->getManager(); 
        $entity  =  $this->getDoctrine()
                         ->getRepository('fibeWWWConfBundle:WwwConf')
                         ->find($wwwConfId);
        if($entity->getConfManager() == $this->get('security.context')->getToken()->getUser() )
        {
            $events = $entity->getConfEvents();
            foreach($events as $event){
                if($event->getLocation())$em->remove($event->getLocation()); 
                $em->remove($event);
            }  
            $em->remove($entity);
            $em->flush();
            return new Response("deleted");
        }
        return new Response("permission denied");
    }
    
    
}
