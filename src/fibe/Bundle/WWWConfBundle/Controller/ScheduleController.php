<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//On insere l'entity Event  de simple schedule

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty;
use IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntityRelation;
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Form\RecurChoiceType;

use fibe\Bundle\WWWConfBundle\Form\XPropertyType; 

use IDCI\Bundle\SimpleScheduleBundle\Form\CalendarEntityRelationType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;





//use fibe\Bundle\WWWConfBundle\Form\EventType; 
//On insere le controlleur de Event 
//use SimpleScheduleBundle\Controller
/**
 * Schedule Controller 
 *
 * @Route("/admin/schedule")
 */
class ScheduleController extends Controller
{

/**
 *  @Route("/", name="wwwconf_schedule")
 *  @Template()
 */
    public function scheduleAction()
    {
        return array();
    }
    

/**
 *  @Route("/conf-{confId}", name="wwwconf_schedule_confId")
 *  @Template("fibeWWWConfBundle:Schedule:schedule.html.twig")
 */
    public function scheduleConfIdAction(Request $request,$confId)
    {
    
        $em = $this->getDoctrine()->getManager(); 
        $entity  =  $this->getDoctrine()
                         ->getRepository('fibeWWWConfBundle:WwwConf')
                         ->find($confId);
        if($entity && $entity->getConfManager() == $this->get('security.context')->getToken()->getUser() )
        {
            return array('currentConf' => $entity);
        }
        return $this->redirect($this->generateUrl('wwwconf_schedule'));
    } 

/**
 *   return all events contained in the given date week
 * @Route("/getEvents/{confId}", name="wwwconf_getevents")
 */
    public function getEventsAction(Request $request,$confId=null)
    {
    
	    $em = $this->getDoctrine()->getManager();
    
	    $getData = $request->query;
	    $methodParam = $getData->get('method', '');
	    $postData = $request->request->all();
        $currentManager=$this->get('security.context')->getToken()->getUser();
	    
      $JSONArray = array();
	    
	    if( $methodParam=="list")
	    {
            $jsdate = $postData['showdate'];
            $type= $postData['viewtype']; 
            if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches)==1){
            $jsdate = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
            //echo $matches[4] ."-". $matches[5] ."-". 0  ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
            }else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
            $jsdate = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
            //echo 0 ."-". 0 ."-". 0 ."-". $matches[1] ."-". $matches[2] ."-". $matches[3];
            }

            //echo $jsdate . "+" . $type;
            switch($type){
            case "month":
              $st = mktime(0, 0, 0, date("m", $jsdate), 1, date("Y", $jsdate));
              $et = mktime(0, 0, -1, date("m", $jsdate)+1, 1, date("Y", $jsdate));
              break;
            case "week":
              //suppose first day of a week is monday 
              $monday  =  date("d", $jsdate) - date('N', $jsdate) + 1;
              //echo date('N', $jsdate);
              $st = mktime(0,0,0,date("m", $jsdate), $monday, date("Y", $jsdate));
              $et = mktime(0,0,-1,date("m", $jsdate), $monday+7, date("Y", $jsdate));
              break;
            case "day":
              $st = mktime(0, 0, 0, date("m", $jsdate), date("d", $jsdate), date("Y", $jsdate));
              $et = mktime(0, 0, -1, date("m", $jsdate), date("d", $jsdate)+1, date("Y", $jsdate));
              break;
            }
            //echo $st . "--" . $et;
           

            $week_start = date($st);
            $week_end = date($et); 
  
            $JSONArray['start'] = $week_start;
            $JSONArray['end'] = $week_end;
            $JSONArray['error'] = null;
            $JSONArray['issort'] = true;

            $eventsEntities=[];
            if($confId==null){
                $confs = $currentManager->getWwwConf();
                foreach($confs as $conf){
                    $events = $conf->getConfEvents();
                    foreach($events as $event){ 
                        $eventsEntities[] = $event;  
                    } 
                }
            }else
            {
                $conf  =  $this->getDoctrine()
                                 ->getRepository('fibeWWWConfBundle:WwwConf')
                                 ->find($confId);
                                 
                if($conf && $conf->getConfManager() == $currentManager){
                    $events = $conf->getConfEvents();
                    foreach($events as $event){ 
                        $eventsEntities[] = $event;  
                    } 
                }else
                {
                    $response = new Response(json_encode("permission denied"));
                }
            }
            $JSONArray['events'] = array();
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
                $JSONArray['events'][] = array(
                    $eventsEntities[$i]->getId(),
                    $eventsEntities[$i]->getSummary(),
                    $start->format('m/d/Y H:i'),
                    $end->format('m/d/Y H:i'),
                    1,                                  // disable alarm clock icon
                    ($duration % 86400 == 86399 || $duration % 86400 == 0 ) ? 1 : 0,     // all day event
                    0,                                  // ??
                    $category?$category->getId():null,                 // color
                    1,                                  // editable
                    $eventsEntities[$i]->getLocation()?$eventsEntities[$i]->getLocation()->getName():null, // location if exists
                    null                                // $attends
                );       
            }

        }else if( $methodParam=="add" && $confId!=null)
        {
            $conf = $this->getDoctrine()
                         ->getRepository('fibeWWWConfBundle:WwwConf')
                         ->find($confId);
                             
            if($conf && $conf->getConfManager() == $currentManager){
                      
                    
                $event= new Event();
                $startAt=new \DateTime($postData['start'], new \DateTimeZone(date_default_timezone_get()));
                $event->setStartAt($startAt );  
                if($postData['isallday']=="true"){
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

                $JSONArray['Data'] = $event->getId();
                $JSONArray['IsSuccess'] = true;
                $JSONArray['Msg'] = "add success";
                
            }else
            {
                $JSONArray['Data'] = $event->getId();
                $JSONArray['IsSuccess'] = false;
                $JSONArray['Msg'] = "permission denied";
            }
              
        }else if( $methodParam=="update")
        { 
                
            $event = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($postData['calendarId']);
            $startAt = new \DateTime($postData['CalendarStartTime'], new \DateTimeZone(date_default_timezone_get()));
            $endAt =new \DateTime($postData['CalendarEndTime'], new \DateTimeZone(date_default_timezone_get()));
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
     * @Route("/editEvents", name="wwwconf_editEvent")
     * @Template()
     */
     
    public function scheduleEditAction(Request $request)
    {
	    $getData = $request->query;
        $id = $getData->get('id', ''); 
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);
          
        
        //confManagerEvents 
        $currentManager=$this->get('security.context')->getToken()->getUser();
        $entities=[]; 
        $confs = $currentManager->getWwwConf();
        foreach($confs as $conf){
            $events = $conf->getConfEvents();
            foreach($events as $event){ 
                $entities[] = $event;  
            } 
        }
        
        if (!in_array($entity, $entities)) {
            throw new AccessDeniedException('Look at your own events !!'); 
        }
        $WwwConf = $entity->getWwwConf();
        //confManagerEvents
        
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $form = $this->createForm(new EventType(), $entity);
        $deleteForm =  $this->createFormBuilder(array('id' => $id))
                            ->add('id', 'hidden')
                            ->getForm()
                        ;

        $xproperty = new XProperty();
        $xproperty->setCalendarEntity($entity);
        $xpropertyForm = $this->createForm(new XPropertyType(), $xproperty);

        $relation = new CalendarEntityRelation();
        $relation->setCalendarEntity($entity);
        $relationForm = $this->createForm(new CalendarEntityRelationType($entity), $relation);

        return array(
            'entity'            => $entity,
            'formEvent'         => $form->createView(),
            'delete_form'       => $deleteForm->createView(),
            'xproperty_form'    => $xpropertyForm->createView(),
            'relation_form'     => $relationForm->createView(),
            'SparqlUrl'         => ($WwwConf?$WwwConf->getConfUri():null)
        );
      
    }
    
     
    /**
     * @Route("/{id}/updateEvents", name="wwwconf_updateEvent") 
     */
     
    public function scheduleUpdateAction(Request $request,$id)
    {
    
      $JSONArray = array();
	     
          
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IDCISimpleScheduleBundle:Event')->find($id);

        if (!$entity) {
          $JSONArray['IsSuccess'] = false;
          $JSONArray['Msg'] = "entity not found"; 
          $response = new Response(json_encode($JSONArray));
          $response->headers->set('Content-Type', 'application/json');
          return $response;
        }
        
        $JSONArray['Data'] = $id;
 
        $editForm = $this->createForm(new EventType(), $entity);
        $editForm->bind($request); 
        if ($editForm->isValid()) { 
            $em->persist($entity);
            $em->flush();
 
          $JSONArray['IsSuccess'] = true;
          $JSONArray['Msg'] = "update success"; 
          
          
        }else{
          $JSONArray['IsSuccess'] = false;
          $JSONArray['Msg'] = "update failed"; 
        
        }
        $response = new Response(json_encode($JSONArray));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

      
    }
    
    /**
     * @Route("/{id}/xpropAdd", name="wwwconf_xproperty_add") 
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
        } else {
            die('todo: flash message');
        }

        $response = new Response(json_encode("ok"));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    
     
    }
}




