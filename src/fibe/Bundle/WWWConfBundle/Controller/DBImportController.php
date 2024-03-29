<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Category; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Location; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty; 
 

/**
 * Api controller.
 *
 * @Route("/admin/link/DBimport")
 */
class DBImportController extends Controller
{

/**
 * @Route("/", name="schedule_admin_DBimport") 
 */
  
      
    public function importAction(Request $request)
    {  
        $JSONFile = json_decode($request->request->get('dataArray'),true); 
        $em = $this->getDoctrine()->getManager(); 
        $entity=null;
        $eventEntities= array();
        $locationEntities= array();
        $categoryEntities= array();
        $wwwConf =  $em->getRepository('fibeWWWConfBundle:WwwConf')->find(1);

        
        
        //////////////////////  locations  //////////////////////
        $locations = $JSONFile['locations'];
        for($i=0;$i<count($locations);$i++){
            $entity= new Location();
            $current = $locations[$i];  
            foreach ($current as $setter => $value) { 
                call_user_func_array(array($entity, $setter), array($value)); 
            } 
            $em->persist($entity); 
            array_push($locationEntities,$entity); 
        }  
        
        
        //////////////////////  categories  ////////////////////// 
        $colorArray = array('lime', 'red', 'blue', 'orange', 'gold', 'coral', 'crimson', 'aquamarine', 'darkOrchid', 'forestGreen', 'peru','purple' ,'seaGreen'  );
        $entities = $JSONFile['categories']; 
        for($i=0;$i<count($entities);$i++){
            $current = $entities[$i]; 
            $existsTest = $this->getDoctrine()
                               ->getRepository('IDCISimpleScheduleBundle:Category')
                               ->findOneBy(array('name' => $current['setName']));
            if($existsTest!=null){
              array_push($categoryEntities,$existsTest); 
              continue; //skip existing category
            }
            $entity= new Category();
            foreach ($current as $setter => $value) {
                //if($setter!="setStartAt" && $setter!="setEndAt")echo "Event->".$setter."(".$value.");\n"; 
                call_user_func_array(array($entity, $setter), array($value)); 
            }
            $entity->setColor($colorArray[$i]);
            $em->persist($entity);
            array_push($categoryEntities,$entity); 
        }  
            
        
        //////////////////////  events  //////////////////////
        $entities = $JSONFile['events'];
        for($i=0;$i<count($entities);$i++){
            $entity= new Event();
            $current = $entities[$i];
            foreach ($current as $setter => $value) {

                if($setter=="setStartAt" || $setter=="setEndAt"){
                    $date= explode(' ', $value); 
                    $value=new \DateTime($date[0], new \DateTimeZone(date_default_timezone_get()));
                    
                }
                
                if($setter=="setLocation"){
                 
                    $value=$locationEntities[$value]; 
                } 
                
                if($setter=="addCategorie"){
                    $value=$categoryEntities[$value];  
                    
                }
                
                if($setter=="setParent"){  
                    // $current["addChild"] = $entities[$value];
                } else{
                    call_user_func_array(array($entity, $setter), array($value)); 
                }
            }
            $entity->setWwwConf(  $wwwConf );
            $em->persist($entity); 
            array_push($eventEntities,$entity); 
        }

        for($i=0;$i<count($entities);$i++){
            $entity= $eventEntities[$i];
            $current = $entities[$i];
            foreach ($current as $setter => $value) {
                if($setter=="setParent"){ 
                    $value=$eventEntities[$value]; 
                    call_user_func_array(array($entity, $setter), array($value));  
                }  
            }
            $entity->setWwwConf(  $wwwConf  );
            $em->persist($entity);
        }

        //echo implode(",\t",$eventEntities)  ;
        //////////////////////  x prop  //////////////////////
        //echo "xproperties->\n";
        $xproperties = $JSONFile['xproperties']; 
        for($i=0;$i<count($xproperties);$i++){
            $current = $xproperties[$i];
            $entity= new XProperty();
            foreach ($current as $setter => $value) { 
                if($setter=="setCalendarEntity"){
                
                    //echo "XProperty->->".$eventEntities[strval($value)]."->".$value.");\n";
                    $value=$eventEntities[$value]; 
                } 
                //echo "XProperty->".$setter."(".$value.");\n";
                call_user_func_array(array($entity, $setter), array($value)); 
            }
            if(!$entity->getXKey())$entity->setXKey(rand (0,9999999999));
            $em->persist($entity);
        }
         
         
        
         
        $em->flush();  

        return new Response("ok");
    } 
    
}

 /** 
  *  
  */ 

/*USEFULL ENTITIES FUNCTION */

    /*EVENT*/
        //setCreatedAt($createdAt) @param: /Datetime 
        //setStartAt($startAt)
        //setSummary($summary)
        //setDescription($description)
        //setOrganizer($organizer)
        //setContacts($contacts)

    /*CalendarEntityRelation*/
        //setRelationType($relationType) {CHILD|SIBLING|PARENT}
        //setCalendarEntity(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntity = null)
        //setRelatedTo(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $relatedTo = null)

    /*XPROPERTY*/
        //setCalendarEntity(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntity = null)
        //setXNamespace($string); {publication_uri|event_uri}
        //setXKey(rand (0,9999999999));//todo AUTO_INCREMENT ??  
        //setXValue($xValue) uri....

    /* ???????????????????????????????????? */
    /*CATEGORIES*/
        //setName($name)
        //setDescription($description)
        //setLevel($level) int
        //addCalendarEntities(\IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity $calendarEntities)


    /* ???????????????????????????????????? */

