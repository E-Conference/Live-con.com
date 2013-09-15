<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//On insere l'entity Event  de simple schedule

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty;
use fibe\Bundle\WWWConfBundle\Entity\Role;

use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Form\RecurChoiceType;
use fibe\Bundle\WWWConfBundle\Form\RoleType;
use fibe\Bundle\WWWConfBundle\Form\ConfEventType;

use fibe\Bundle\WWWConfBundle\Form\XPropertyType; 


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;





//use fibe\Bundle\WWWConfBundle\Form\EventType; 
//On insere le controlleur de Event 
//use SimpleScheduleBundle\Controller
/**
 * Schedule Controller 
 *
 * @Route("/")
 */
class ScheduleController extends Controller
{

/**
 *  @Route("/", name="schedule_index")
 *  @Template()
 */
    public function indexAction()
    {

        $conf = $this->getDoctrine()
                     ->getRepository('fibeWWWConfBundle:WwwConf')
                     ->find(1); 
        return array('currentConf' => $conf);     
    
}    

/**
 *  @Route("/view", name="schedule_view")
 *  @Template()
 */
    public function scheduleAction()
    {

        $em = $this->getDoctrine();
        $conf = $em->getRepository('fibeWWWConfBundle:WwwConf')
                 ->find(1);
        $categories = $em->getRepository('IDCISimpleScheduleBundle:Category')->getOrdered();
        $locations = $em->getRepository('IDCISimpleScheduleBundle:Location')->findAll();
        $themes = $em->getRepository('fibeWWWConfBundle:Theme')->findAll();

        return array(
                'currentConf' => $conf,
                'categories'  => $categories,
                'themes'   => $themes
            );     
    
}
  
 
/**
 *   return all events contained in the given date week
 * @Route("/getEvents", name="schedule_view_event_get")
 */
    public function getEventsAction(Request $request)
    {
    
        $em = $this->getDoctrine()->getManager();
    
        $getData = $request->query;
        $methodParam = $getData->get('method', '');
        $postData = $request->request->all();
        $currentManager=$this->get('security.context')->getToken()->getUser();
        
        $JSONArray = array();
        if( $methodParam=="add" )
        {
            $conf = $this->getDoctrine()
                         ->getRepository('fibeWWWConfBundle:WwwConf')
                         ->find(1); 
                
                $event= new Event(); 
                $event->setEndAt(new \DateTime($postData['end'], new \DateTimeZone(date_default_timezone_get()))); 
                $event->setStartAt(new \DateTime($postData['start'], new \DateTimeZone(date_default_timezone_get())));  
                $event->setSummary( $postData['title'] );
                $event->setIsAllDay($postData['allDay']=="true") ;

                $event->setWwwConf($conf);
                
                $em->persist($event);
                $em->flush();

                $JSONArray['id'] = $event->getId();
                $JSONArray['IsSuccess'] = true;
                $JSONArray['Msg'] = "add success"; 
        }else if( $methodParam=="update")
        { 
                
            $event = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($postData['id']);
            $startAt = new \DateTime($postData['start'], new \DateTimeZone(date_default_timezone_get()));
            $endAt =new \DateTime($postData['end'], new \DateTimeZone(date_default_timezone_get()));
            
            $event->setStartAt( $startAt );
            $event->setEndAt( $endAt );
            $event->setParent( $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($postData['parent']['id']) );
            $event->setSummary( $postData['title'] );
            $event->setIsAllDay($postData['allDay']=="true") ;
            $em->persist($event);
            $em->flush();
            $JSONArray['IsSuccess'] = true;
            $JSONArray['Msg'] = "Successfully";
        }
        
        $response = new Response(json_encode($JSONArray));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    

    /**
     * @Route("/editEvents", name="schedule_view_event_edit")
     * @Template()
     */
     
    public function scheduleEditAction(Request $request)
    {
	    $getData = $request->query;
        $id = $getData->get('id', ''); 
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);
          
        $conf = $em->getRepository('fibeWWWConfBundle:WwwConf')
                    ->find(1); 
         
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }

        $role = new Role();
        $roleForm = $this->createForm(new RoleType(), $role);
        $editForm = $this->createForm(new ConfEventType(), $entity);
       
         $form_paper = $this->createFormBuilder($entity)

            ->add('papers', 'entity', array(
                      'class'    => 'fibeWWWConfBundle:Paper',
                      'property' => 'title',
                      'required' => false,
                      'multiple' => false))
            ->getForm();

         $form_theme = $this->createFormBuilder($entity)
            ->add('themes', 'entity', array(
                  'class'    => 'fibeWWWConfBundle:Theme',
                  'required' => false,
                  'property' => 'libelle',
                  'multiple' => false))
            ->getForm();
            
        $deleteForm = $this->createDeleteForm($id);
 
        return $this->render('fibeWWWConfBundle:Schedule:scheduleEdit.html.twig', array(
            'entity'     => $entity,
            'edit_form'  => $editForm->createView(),
            'role_form'  => $roleForm->createView(),
            'paper_form' => $form_paper->createView(),
            'theme_form' => $form_theme->createView(),
            'delete_form' => $deleteForm->createView()
        ));

    }
    
     
    /**
     * ajax version of event edit controller
     * @Route("/{id}/updateEvents", name="schedule_view_event_update") 
     */
     
    public function scheduleUpdateAction(Request $request,$id)
    {
    
      $JSONArray = array();
	     
          
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:ConfEvent')->find($id);

        if ($entity) {

            $JSONArray['Data'] = $id;
     
            $editForm = $this->createForm(new EventType(), $entity);
            $editForm->bind($request); 
            if ($editForm->isValid()) { 
                $em->persist($entity);
                $em->flush();
     
              $JSONArray['IsSuccess'] = true;
              $JSONArray['Msg'] = "update succses";
            }else{
              $JSONArray['IsSuccess'] = false;
              $JSONArray['Msg'] = "update failed";
            }
        }else{

          $JSONArray['IsSuccess'] = false;
          $JSONArray['Msg'] = "entity not found";
        }
        
        $response = new Response(json_encode($JSONArray));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

      
    }
    
    /**
     * Override dimplescehdule controller to provide json response
     * @Route("/{id}/xpropAdd", name="schedule_xproperty_add") 
     */
     
    public function xpropAddAction(Request $request,$id)
    {
    
        $em = $this->getDoctrine()->getManager();
        $calendarEntity = $em->getRepository('IDCISimpleScheduleBundle:CalendarEntity')->find($id);

        if (!$calendarEntity) {
            throw $this->createNotFoundException('Unable to find Calendar entity.');
        }

        $entity = new XProperty();
        $form = $this->createForm(new XPropertyType, $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->container->get('session')->getFlashBag()->add(
                     'success',
                     'Event successfully updated'
                     );
        } else {
            $this->container->get('session')->getFlashBag()->add(
                     'error',
                     'Submission failed'
                     );
        }

        $response = new Response(json_encode("ok"));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    
     
    }
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}




