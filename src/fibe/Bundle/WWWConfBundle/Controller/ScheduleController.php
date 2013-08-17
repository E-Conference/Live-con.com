<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//On insere l'entity Event  de simple schedule

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty;

use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Form\RecurChoiceType;

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

        $conf = $this->getDoctrine()
                     ->getRepository('fibeWWWConfBundle:WwwConf')
                     ->find(1); 
        $logger = $this->get('logger');
$logger->info('Nous avons récupéré le logger');
        return array('currentConf' => $conf);     
    
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
	    
	    if( $methodParam=="list")
	    {
            // $type= $postData['viewtype']; 
            // $jsdate = $postData['showdate'];
            // if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches)==1){
            // $jsdate = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
            // //echo $matches[4] ."-". $matches[5] ."-". 0  ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
            // }else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
            // $jsdate = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
            // //echo 0 ."-". 0 ."-". 0 ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
            // }

            // //echo $jsdate . "+" . $type;
            // switch($type){
            // case "month":
            //   $st = mktime(0, 0, 0, date("m", $jsdate), 1, date("Y", $jsdate));
            //   $et = mktime(0, 0, -1, date("m", $jsdate)+1, 1, date("Y", $jsdate));
            //   break;
            // case "week":
            //   //suppose first day of a week is monday 
            //   $monday  =  date("d", $jsdate) - date('N', $jsdate) + 1;
            //   //echo date('N', $jsdate);
            //   $st = mktime(0,0,0,date("m", $jsdate), $monday, date("Y", $jsdate));
            //   $et = mktime(0,0,-1,date("m", $jsdate), $monday+7, date("Y", $jsdate));
            //   break;
            // case "day":
            //   $st = mktime(0, 0, 0, date("m", $jsdate), date("d", $jsdate), date("Y", $jsdate));
            //   $et = mktime(0, 0, -1, date("m", $jsdate), date("d", $jsdate)+1, date("Y", $jsdate));
            //   break;
            // }
            //echo $st . "--" . $et;
            

            // $week_start = date($getData->get('start', ''));
            // $week_end = date($getData->get('end', '')); 
  
            // $JSONArray['start'] = $week_start;
            // $JSONArray['end'] = $week_end;
            $JSONArray['error'] = null;
            $JSONArray['issort'] = true;

            $eventsEntities = $em->getRepository('fibeWWWConfBundle:ConfEvent')->findAll();

            $JSONArray['events'] = array();
            $JSONArray['instant_events'] = array();
            for ($i = 0; $i < count($eventsEntities); $i++) {

                $start =  $eventsEntities[$i]->getStartAt() ; 
                $end =  $eventsEntities[$i]->getEndAt() ; 
                $duration =   $end->diff($start) ; 

                $duration = ($duration->y * 365 * 24 * 60 * 60) + 
                            ($duration->m * 30 * 24 * 60 * 60) + 
                            ($duration->d * 24 * 60 * 60) + 
                            ($duration->h * 60 * 60) + 
                            ($duration->i * 60) + 
                            $duration->s; 
                //echo $eventsEntities[$i]->getSummary().", ".$duration % 86400 ." .... ";
                $category = $eventsEntities[$i]->getCategories();
                $category = $category[0];
                $event = array(
                    "id" => $eventsEntities[$i]->getId(),
                    "duration" => $duration,
                    "title" => $eventsEntities[$i]->getSummary(),
                    "allDay" => (($duration+86400) % 86400 == 86399 || ($duration+86400) % 86400 == 0 ) && ($duration !== 1 || $duration !== 0)  ? 1 : 0,     // all day event
                    "start" => $start->format('m/d/Y H:i'),
                    "end" => $end->format('m/d/Y H:i'),
                    "color" => $category?$category->getColor():null,                 // color
                );       
                if($duration !== 1 && $duration !== 0)
                {
                    $JSONArray['events'][] = $event;
                }else
                {
                    $JSONArray['instant_events'][] = $event;
                }
            }

        }else if( $methodParam=="add" )
        {
            $conf = $this->getDoctrine()
                         ->getRepository('fibeWWWConfBundle:WwwConf')
                         ->find(1); 
                
                $event= new Event();
                $startAt=new \DateTime($postData['start'], new \DateTimeZone(date_default_timezone_get()));
                $event->setStartAt($startAt );  
                if($postData['allDay']=="true"){
                  $endAt = new \DateTime($postData['end'], new \DateTimeZone(date_default_timezone_get()));   
                  $event->setEndAt($endAt->add(new \DateInterval('PT23H59M59S'))); 
                }
                else {
                  $event->setEndAt(new \DateTime($postData['end'], new \DateTimeZone(date_default_timezone_get()))); 
                }
                $event->setSummary($postData['title']);
                $event->setWwwConf($conf);
                
                $em->persist($event);
                $em->flush();  

                $JSONArray['id'] = $event->getId();
                $JSONArray['IsSuccess'] = true;
                $JSONArray['Msg'] = "add success"; 
        }else if( $methodParam=="update")
        { 
                
            $event = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($postData['id']);
            $startAt = new \DateTime($postData['start'], new \DateTimeZone(date_default_timezone_get()));
            $endAt =new \DateTime($postData['end'], new \DateTimeZone(date_default_timezone_get()));
            $event->setStartAt( $startAt );
            $event->setEndAt( $endAt );
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
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);
          
        $conf = $em->getRepository('fibeWWWConfBundle:WwwConf')
                    ->find(1); 
         
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $form = $this->createForm(new EventType(), $entity);
        $deleteForm =  $this->createFormBuilder(array('id' => $id))
                            ->add('id', 'hidden')
                            ->getForm();

        $xproperty = new XProperty();
        $xproperty->setCalendarEntity($entity);
        $xpropertyForm = $this->createForm(new XPropertyType(), $xproperty);

        
        return array(
            'entity'            => $entity,
            'formEvent'         => $form->createView(),
            'delete_form'       => $deleteForm->createView(),
            'xproperty_form'    => $xpropertyForm->createView(),
            'SparqlUrl'         => $conf->getConfUri()
        );
      
    }
    
     
    /**
     * @Route("/{id}/updateEvents", name="schedule_view_event_update") 
     */
     
    public function scheduleUpdateAction(Request $request,$id)
    {
    
      $JSONArray = array();
	     
          
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);

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
}




