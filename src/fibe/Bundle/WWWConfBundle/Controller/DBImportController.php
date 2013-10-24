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
use fibe\Bundle\WWWConfBundle\Entity\Keyword;
use fibe\Bundle\WWWConfBundle\Entity\Organization;
use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Entity\Role;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;

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
        // $JSONFile = $request->request->get('dataArray');          
        $JSONFile = json_decode($request->request->get('dataArray'),true); 
        
        $em = $this->getDoctrine()->getManager(); 
        $entity=null;
        $eventEntities= array();
        $personEntities= array(); 
        $locationEntities= array();
        $categoryEntities= array();
        $themeEntities= array();
        $organizationEntities= array();
        $keywordEntities= array();
        $proceedingEntities= array();
        $wwwConf =  $this->getUser()->getCurrentConf();

        //categories color.
        $colorArray = array('lime', 'red', 'blue', 'orange', 'gold', 'coral', 'crimson', 'aquamarine', 'darkOrchid', 'forestGreen', 'peru','purple' ,'seaGreen'  );
        

        
        
        //////////////////////  keywords  ////////////////////// 
        if(isset($JSONFile['keywords'])){
            $keywords = $JSONFile['keywords'];
            for($i=0;$i<count($keywords);$i++){
                $current = $keywords[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Keyword')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                  array_push($keywordEntities,$existsTest); 
                  continue; //skip existing category
                }
                $entity= new Keyword();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($keywordEntities,$entity); 
            }  
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
                  continue; //skip existing category
                }
                $entity= new Location();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($locationEntities,$entity); 
            }  
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
                  continue; //skip existing category
                }

                $entity= new Organization();
                foreach ($current as $setter => $value) {

                    call_user_func_array(array($entity, $setter), array($value)); 
                }
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($organizationEntities,$entity); 
            }  
        }     
        
        //////////////////////  persons  ////////////////////// 
        if(isset($JSONFile['persons'])){
            $entities = $JSONFile['persons']; 
            for($i=0;$i<count($entities);$i++){
                $current = $entities[$i];   

                $entity= new Person();
                foreach ($current as $setter => $value) {
                    //if($setter!="setStartAt" && $setter!="setEndAt")echo "Event->".$setter."(".$value.");\n"; 
                    if($setter=="addOrganization"){
                        
                        $j=0;
                        foreach ($value as $organization) {
                            if($j!=0){
                                $val=$organizationEntities[$organization];

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        }  
                        $value=$organizationEntities[$value[0]];  
                    }
                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($personEntities,$entity);  
            }  
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
                  continue; //skip existing category
                }
                $entity= new Paper();
                foreach ($current as $setter => $value) { 
                    if($setter=="addSubject"){
                        //addKeyword is an array of index
                        
                        $j=0;
                        foreach ($value as $keyword) {
                            if($j!=0){
                                $val=$keywordEntities[$keyword];

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        } 
                        $value=$keywordEntities[$value[0]];  
                    }

                    if($setter=="addAuthor"){
                        //addAuthor is an array of index
                        
                        $j=0;
                        foreach ($value as $persons) {
                            if($j!=0){
                                $val=$personEntities[$persons];

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        } 
                        $value=$personEntities[$value[0]];  
                    }

                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($proceedingEntities,$entity); 
            }  
        }   
        
        
        //////////////////////  themes  //////////////////////
        if(isset($JSONFile['themes'])){
            $themes = $JSONFile['themes'];
            for($i=0;$i<count($themes);$i++){
                $current = $themes[$i];  
                $existsTest = $this->getDoctrine()
                                   ->getRepository('fibeWWWConfBundle:Theme')
                                   ->findOneBy(array('name' => $current['setName']));
                if($existsTest!=null){
                  array_push($themeEntities,$existsTest); 
                  continue; //skip existing category
                }
                $entity= new Theme();
                foreach ($current as $setter => $value) { 
                    call_user_func_array(array($entity, $setter), array($value)); 
                } 
                $entity->setConference(  $wwwConf );
                $em->persist($entity); 
                array_push($themeEntities,$entity); 
            }  
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
                }
                $entity= new Category();
                foreach ($current as $setter => $value) {
                    //if($setter!="setStartAt" && $setter!="setEndAt")echo "Event->".$setter."(".$value.");\n"; 
                    call_user_func_array(array($entity, $setter), array($value)); 
                }
                // $entity->setConference(  $wwwConf );
                $entity->setColor($colorArray[$j++]); //colorless categories
                $em->persist($entity);
                array_push($categoryEntities,$entity); 
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
                    
                    if($setter=="addChair"){
                        $j=0;

                        //retrieve Chair roletype
                        $chairRoleType = $this->getDoctrine()
                                           ->getRepository('fibeWWWConfBundle:RoleType')
                                           ->findOneBy(array('name' => 'Chair'));
                        if($chairRoleType==null){
                            $chairRoleType = new RoleType();
                            $chairRoleType->setName("Chair");
                            $em->persist($chairRoleType);
                        }


                        $setter = "addRole";
                        foreach ($value as $chair) {
                            if($j!=0){
                                $person=$personEntities[$chair];
                                $val = new Role();
                                $val->setType($chairRoleType);
                                $val->setPerson($person);

                                call_user_func_array(array($entity, $setter), array($val));
                            }
                            $j++;
                        }  
                        $person=$personEntities[$value[0]];
                        $value = new Role();
                        $value->setType($chairRoleType);
                        $value->setPerson($person);
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
                $entity->setConference(  $wwwConf );
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
                $entity->setConference(  $wwwConf  );
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