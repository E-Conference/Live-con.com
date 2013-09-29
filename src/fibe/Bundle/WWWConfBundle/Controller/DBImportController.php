<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event; 
use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\Theme;
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
        $JSONFile = $request->request->get('dataArray'); 
        $em = $this->getDoctrine()->getManager(); 
        $entity=null;
        $eventEntities= array();
        $personEntities= array();
        $locationEntities= array();
        $categoryEntities= array();
        $themeEntities= array();
        $wwwConf =  $em->getRepository('fibeWWWConfBundle:WwwConf')->find(1);

        //categories color.
        $colorArray = array('lime', 'red', 'blue', 'orange', 'gold', 'coral', 'crimson', 'aquamarine', 'darkOrchid', 'forestGreen', 'peru','purple' ,'seaGreen'  );
        

        
        
        //////////////////////  locations  ////////////////////// 
        if(isset($JSONFile['locations'])){
            $locations = $JSONFile['locations'];
            for($i=0;$i<count($locations);$i++){
                $current = $locations[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('IDCISimpleScheduleBundle:Location')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                  array_push($locationEntities,$existsTest); 
                  continue; //skip existing category
                }
                $entity= new Location();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $em->persist($entity); 
                array_push($locationEntities,$entity); 
            }  
        }     
        
        
        //////////////////////  themes  //////////////////////
        if(isset($JSONFile['themes'])){
            $theme = $JSONFile['themes'];
            for($i=0;$i<count($theme);$i++){
                $current = $theme[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Theme')
                                   ->findOneBy(array('libelle' => $current['setLibelle']));
                if($existsTest!=null){
                  array_push($themeEntities,$existsTest); 
                  continue; //skip existing category
                }
                $entity= new Theme();
                foreach ($current as $setter => $value) { 
                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $em->persist($entity); 
                array_push($themeEntities,$entity); 
            }  
        }     
        
        
        //////////////////////  categories  ////////////////////// 
        if(isset($JSONFile['categories'])){
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
        }
        
        //////////////////////  person  ////////////////////// 
        if(isset($JSONFile['persons'])){
            $entities = $JSONFile['persons']; 
            for($i=0;$i<count($entities);$i++){
                $current = $entities[$i];  
                $entity= new Person();
                foreach ($current as $setter => $value) {
                    //if($setter!="setStartAt" && $setter!="setEndAt")echo "Event->".$setter."(".$value.");\n"; 
                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $em->persist($entity);
                array_push($personEntities,$entity); 
            }  
        }    
        
        //////////////////////  events  //////////////////////
        if(isset($JSONFile['events'])){
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
                    
                    if($setter=="addTheme"){
                        $j=0;
                        foreach ($value as $theme) {
                            if($j!=0){
                                $val=$themeEntities[$theme];

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        } 
                        $value=$themeEntities[$value[0]];  
                        
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

            //parent / child relationship
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
        }
        //echo implode(",\t",$eventEntities)  ;
        //////////////////////  x prop  //////////////////////
        //echo "xproperties->\n";
        if(isset($JSONFile['xproperties'])){
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

