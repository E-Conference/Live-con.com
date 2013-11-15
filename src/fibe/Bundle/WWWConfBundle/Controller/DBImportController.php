<?php 
namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event; 
use fibe\Bundle\WWWConfBundle\Entity\Person; 
use fibe\Bundle\WWWConfBundle\Entity\Topic; 
use fibe\Bundle\WWWConfBundle\Entity\Organization;
use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Entity\Role;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;
use fibe\Bundle\WWWConfBundle\Entity\SocialService;
use fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount;

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
        $wwwConf =  $this->getUser()->getCurrentConf();
        $mainConfEvent = $wwwConf->getMainConfEvent();

        $eventEntities        = array();
        $personEntities       = array(); 
        $locationEntities     = array();
        $categoryEntities     = array();
        $topicEntities        = array();
        $organizationEntities = array();
        $topicEntities        = array();
        $proceedingEntities   = array();


        $defaultCategory = $this->getDoctrine()
                                   ->getRepository('IDCISimpleScheduleBundle:Category')
                                   ->findOneBy(array('name' => 'TalkEvent'));

        //categories color.
        $colorArray = array('lime', 'red', 'blue', 'orange', 'gold', 'coral', 'crimson', 'aquamarine', 'darkOrchid', 'forestGreen', 'peru','purple' ,'seaGreen'  );
        
        ////////////////////// conference //////////////////////
        if(isset($JSONFile['conference'])){
            $conference = $JSONFile['conference'];
            foreach ($conference as $setter => $value) {

                if($setter=="setAcronym"){
                    $entity = $wwwConf;
                }else{
                    $entity = $mainConfEvent;
                }
                call_user_func_array(array($entity, $setter), array($value)); 
            }
            $conference = null;
        }
        
        
        //////////////////////  topics  ////////////////////// 
        if(isset($JSONFile['topics'])){
            $topics = $JSONFile['topics'];
            for($i=0;$i<count($topics);$i++){
                $current = $topics[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Topic')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                  array_push($topicEntities,$existsTest); 
                  continue; //skip existing topic
                }
                $entity= new Topic();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($topicEntities,$entity); 
            }  
            $topics = null;
        }   
        
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
                  continue; //skip existing location
                }
                $entity= new Location();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($locationEntities,$entity); 
            }  
            $locations = null;
        }   
        
        //////////////////////  organizations  //////////////////////
        if(isset($JSONFile['organizations'])){
            $organizations = $JSONFile['organizations'];
            for($i=0;$i<count($organizations);$i++){
                $current = $organizations[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Organization')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                  array_push($organizationEntities,$existsTest);
                  continue; //skip existing organization
                }

                $entity= new Organization();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                }
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($organizationEntities,$entity); 
            }  
            $organizations = null;
        }     
        
        //////////////////////  persons  ////////////////////// 
        if(isset($JSONFile['persons'])){
            $entities = $JSONFile['persons']; 
            for($i=0;$i<count($entities);$i++){
                $current = $entities[$i];   

                $entity= new Person();
                foreach ($current as $setter => $value) { 

                    if($setter == "setTwitter"){
                        //get twitter entity
                        $ss = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:SocialService')
                                   ->findOneBy(array('name' => 'Twitter'));
                        //create account     
                        $ssa = new SocialServiceAccount();
                        $ssa->setAccountName($value)
                            ->setSocialService($ss);
                        $value = $ssa;
                        $setter = 'addAccount';
                    }
                    if(is_array($value)){
                        switch ($setter) {
                            case 'addOrganization':
                                $entityArray = $organizationEntities;
                            break;  
                        } 
                        $this->doArray($entityArray,$entity, $setter,$value);
                    }else{
                        call_user_func_array(array($entity, $setter), array($value)); 
                    } 
                } 
                
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($personEntities,$entity);
            }  
            $entities = null;
        }    
        
        
        //////////////////////  proceedings  //////////////////////
        if(isset($JSONFile['proceedings'])){
            $proceedings = $JSONFile['proceedings'];
            for($i=0;$i<count($proceedings);$i++){
                $current = $proceedings[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Paper')
                                   ->findOneBy(array('title' => $current['setTitle']));
                if($existsTest!=null){
                  array_push($proceedingEntities,$existsTest); 
                  continue; //skip existing paper
                }
                $entity= new Paper();
                foreach ($current as $setter => $value) {  
                    if(is_array($value)){
                        switch ($setter) {
                            case 'addTopic':
                                $entityArray = $topicEntities;
                            break; 
                            case 'addAuthor':
                                $entityArray = $personEntities;
                            break; 
                        } 
                        $this->doArray($entityArray,$entity, $setter,$value);
                    }else{
                        call_user_func_array(array($entity, $setter), array($value)); 
                    }

                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($proceedingEntities,$entity); 
            }  
            $proceedings = null;
        }   
        
        
        //////////////////////  categories  ////////////////////// 
        if(isset($JSONFile['categories'])){
            $entities = $JSONFile['categories'];
            $j=0;
            for($i=0;$i<count($entities);$i++){
                $current = $entities[$i]; 
                $existsTest = $this->getDoctrine()
                                   ->getRepository('IDCISimpleScheduleBundle:Category')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                    array_push($categoryEntities,$existsTest); 
                    continue; //skip existing category
                }else{
                    array_push($categoryEntities,$defaultCategory);  
                }    
                // echo $current['setName']. " don't exists<br/>";

                

            }  
            // for ($i=0; $i < count($categoryEntities); $i++) { 
            //     echo $i. " " . $categoryEntities[$i]->getName()."<br/>";
            // } 
            $entities = null;
        }


        //retrieve Chair roletype
        $chairRoleType = $this->getDoctrine()
                           ->getRepository('fibeWWWConfBundle:RoleType')
                           ->findOneBy(array('name' => 'Chair'));
        //retrieve Presenter roletype
        $presenterRoleType = $this->getDoctrine()
                           ->getRepository('fibeWWWConfBundle:RoleType')
                           ->findOneBy(array('name' => 'Presenter'));

        
        //////////////////////  events  //////////////////////
        if(isset($JSONFile['events'])){
            $entities = $JSONFile['events'];
            for($i=0;$i<count($entities);$i++){
                $entity= new Event();
                $current = $entities[$i]; 
                echo" TAMERE " . $setter;
                foreach ($current as $setter => $value) {
                    echo" TAMERE " . $setter;

                    if($setter=="setStartAt" || $setter=="setEndAt"){
                        $date= explode(' ', $value); 
                        $value=new \DateTime($date[0], new \DateTimeZone(date_default_timezone_get()));
                    }
                    
                    if($setter=="setLocation"){
                        $value=$locationEntities[$value];
                    }
                    
                    if($setter=="addCategorie"){
                        if(count($categoryEntities) <= $value){
                            // echo count($categoryEntities)." ".$value." ".$entity->getSummary()."<br/>";
                            $value = $defaultCategory;
                        }else{
                            $value=$categoryEntities[$value];
                        }
                    }
                    
                    if($setter=="addPaper"){
                        $j=0;
                        foreach ($value as $paper) {
                            if($j!=0){
                                $val=$proceedingEntities[$paper];

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        } 
                        $value=$proceedingEntities[$value[0]];   
                    }
                     
                    if($setter=="setParent"){continue;}

                    if($setter=="mainConferenceEvent"){ 
                        $wwwConf->setMainConfEvent($entity);
                        $entity->setIsMainConfEvent(true);
                        $em->remove($mainConfEvent);
                        $mainConfEvent = $entity;
                        continue;
                    }


                    if(is_array($value)){
                        switch ($setter) {
                            case 'addTopic':
                                $entityArray = $topicEntities;
                            break; 
                            case 'addPaper':
                                $entityArray = $proceedingEntities;
                            break;  
                        } 
                        if($setter=="addChair"){

                            $setter = "addRole";
                            foreach ($value as $chair) { 
                                    $val = new Role();
                                    $val->setType($chairRoleType);
                                    $val->setPerson($personEntities[$chair]);
                                    $val->setEvent($entity);
                                    $entity->addRole($val);
 
                            }  
                        }else if($setter=="addPresenter"){

                            $setter = "addRole";
                            foreach ($value as $presenter) {  
                                    $val = new Role();
                                    $val->setType($presenterRoleType);
                                    $val->setPerson($personEntities[$presenter]);
                                    $val->setEvent($entity);
                                    $entity->addRole($val);
                            }   
                        }else {
                            $this->doArray($entityArray,$entity, $setter,$value); 
                        }
                    } else {
                        call_user_func_array(array($entity, $setter), array($value)); 
                    } 
                }
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($eventEntities,$entity); 
            }

            //parent / child relationship
            for($i=0;$i<count($entities);$i++){
                $entity= $eventEntities[$i];
                $current = $entities[$i];
                $hasParent = false;
                foreach ($current as $setter => $value) {
                    if($setter=="setParent"){ 
                        $hasParent = true;
                        $value=$eventEntities[$value]; 
                        call_user_func_array(array($entity, $setter), array($value));  
                    }
                }
                if(!$hasParent){
                    $entity->setParent($mainConfEvent);
                }
                $entity->setConference($wwwConf);
                $em->persist($entity);
            }
            $entities = null;
        }

        //echo implode(",\t",$eventEntities)  ;
        //////////////////////  x prop  //////////////////////
        //echo "xproperties->\n";
        // if(isset($JSONFile['xproperties'])){
        //     $xproperties = $JSONFile['xproperties']; 
        //     for($i=0;$i<count($xproperties);$i++){
        //         $current = $xproperties[$i];
        //         $entity= new XProperty();
        //         foreach ($current as $setter => $value) { 
        //             if($setter=="setCalendarEntity"){
                    
        //                 //echo "XProperty->->".$eventEntities[strval($value)]."->".$value.");\n";
        //                 $value=$eventEntities[$value]; 
        //             } 
        //             //echo "XProperty->".$setter."(".$value.");\n";
        //             call_user_func_array(array($entity, $setter), array($value)); 
        //         }
        //         if(!$entity->getXKey())$entity->setXKey(rand (0,9999999999));
        //         $em->persist($entity);
        //     }
        // }
         
        $mainConfEvent->setParent(null);
        $em->persist($mainConfEvent);
        $em->persist($wwwConf);

        //finally, make sure every events are at least child of the main conf event
        
        $confEvents = $wwwConf->getEvents(); 
        foreach ($confEvents as $event) {
            if(!$event->getParent()){
                $event->setParent($mainConfEvent);
                $em->persist($event);
            }
        }
        $mainConfEvent->setParent(null);
        $em->persist($mainConfEvent);
        
        $em->flush();

        return new Response("ok");
    } 

    private function doArray($entityArray, $entity, $setter, $valArray){ 
        foreach ($valArray as $e) { 

            $val=$entityArray[$e]; 
            call_user_func_array(array($entity, $setter), array($val));
        } 
    }    
} 