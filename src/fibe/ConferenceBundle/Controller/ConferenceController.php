<?php

namespace fibe\ConferenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
use fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig;
use fibe\Bundle\WWWConfBundle\Form\WwwConfType;
use fibe\Bundle\WWWConfBundle\Form\ModuleType;
use fibe\Bundle\WWWConfBundle\Form\WwwConfEventDefaultType;
use fibe\Bundle\WWWConfBundle\Form\WwwConfDefaultType;

use fibe\SecurityBundle\Entity\Authorization;

use IDCI\Bundle\SimpleScheduleBundle\Form\XPropertyType; 
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Event; 
use IDCI\Bundle\SimpleScheduleBundle\Entity\Location; 
use fibe\Bundle\WWWConfBundle\Entity\Module;




use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * Link controller.
 *
 * @Route("/admin/conference")
 */
class ConferenceController extends Controller
{


/**
 * @Route("/edit", name="schedule_conference_edit")
 * 
 * @Template()
 */
    public function editAction(Request $request)
    {    
        $em = $this->getDoctrine()->getManager();       
        $wwwConf = $this->getUser()->getCurrentConf();
        //main conf event MUST have a location
        $mainConfEvent = $wwwConf->getMainConfEvent(); 
        $form = $this->createForm(new WwwConfType($this->getUser()), $wwwConf);

      //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            //throw new AccessDeniedException('Action not authorized !');
           return $this->redirect($this->generateUrl('schedule_conference_show'));
          } 
 
            $request = $this->get('request');
            if ($request->getMethod() == 'POST') {
              $form->bind($request);
           
              if ($form->isValid()) {
                  $em = $this->getDoctrine()->getManager();
                  $wwwConf->slugify();
                  $em->persist($wwwConf);
                  $wwwConf->uploadLogo();
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
              'location' => $mainConfEvent->getLocation(),
              'wwwConf'  => $wwwConf,
              'form'     => $form->createView(),
              'authorized' => $authorization->getFlagconfDatas()
              );
        
    }


    /**
     * @Route("/show", name="schedule_conference_show")
     * 
     * @Template()
     */
    public function showAction(Request $request)
    {    
        $em = $this->getDoctrine()->getManager();       
        $wwwConf = $this->getUser()->getCurrentConf();
        //main conf event MUST have a location
        $mainConfEvent = $wwwConf->getMainConfEvent(); 

      //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
            
        return array(
              'location' => $mainConfEvent->getLocation(),
              'wwwConf'  => $wwwConf,
              'authorized' => $authorization->getFlagconfDatas()
              );
        
    }


  /**
   * @Route("/{id}/empty", name="schedule_conference_empty") 
   */
    public function emptyAction(Request $request,$id)
    {
      $em = $this->getDoctrine()->getManager();

      $conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);
      if (!$conference) {
            throw $this->createNotFoundException('Unable to find Conference.');
        }

      //TODO CSRF TOKEN
      // $csrf = $this->get('form.csrf_provider'); //Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider by default
      // $token = $csrf->generateCsrfToken($intention); //Intention should be empty string, if you did not define it in parameters
      // BOOLEAN $csrf->isCsrfTokenValid($intention, $token);

      //TODO CHECK RIGHT super_admin
      
      //check if the processed conference belongs to the user
      $user=$this->getUser();
      if (!$user->authorizedAccesToConference($conference)) {
          throw new AccessDeniedException('Look at your conferences !!!');
      } 
      //Authorization Verification conference datas manager
    
      $authorization = $user->getAuthorizationByConference($conference);
      if(!$authorization->getFlagconfDatas()){
        throw new AccessDeniedException('Action not authorized !');
      } 
 
      $emptyConf = $this->get('emptyConf');
      $emptyConf->emptyConf($conference,$em);

      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
                'success',
                'conference successfully emptied.'
            );
      return $this->redirect($this->generateUrl('schedule_conference_edit'));
    }

     /**
     * Creates a new ConfEvent entity.
     *  @Route("/create", name="schedule_conference_create")
     */
    public function createAction(Request $request)
    {
            //Persist Conference
            $em = $this->getDoctrine()->getManager();
        
            //Create the default conference
            $entity = new WwwConf();
            $entity->setLogoPath("livecon.png"); 
            $em->persist($entity); 

            //Session user 
            $user = $this->getUser();

            //Module
            $defaultModule = new Module();
            $defaultModule->setPaperModule(1);
            $defaultModule->setOrganizationModule(1);
            $em->persist($defaultModule);

            //Create new App config for the conference
            $defaultAppConfig = new MobileAppConfig();

            //header color
            $defaultAppConfig->setBGColorHeader("#f2f2f2");
            $defaultAppConfig->setTitleColorHeader("#000000");
            //navBar color
            $defaultAppConfig->setBGColorNavBar("#305c6b");
            $defaultAppConfig->setTitleColorNavBar("#f3f6f6");
            //content color
            $defaultAppConfig->setBGColorContent("#f3f6f6");
            $defaultAppConfig->setTitleColorContent("#8c949c");
            //buttons color 
            $defaultAppConfig->setBGColorButton("#f3f6f6");
            $defaultAppConfig->setTitleColorButton("#000000");
            //footer color
            $defaultAppConfig->setBGColorfooter("#305c6b");
            $defaultAppConfig->setTitleColorFooter("#f3f6f6");
            $defaultAppConfig->setIsPublished(true);
            $defaultAppConfig->setDblpDatasource(true);
            $defaultAppConfig->setGoogleDatasource(true);
            $defaultAppConfig->setDuckduckgoDatasource(true);
           
            $em->persist($defaultAppConfig);

            $categorie = $em->getRepository('IDCISimpleScheduleBundle:Category')->findOneByName("ConferenceEvent");
            
           //Main conf event  
            $mainConfEvent = new ConfEvent();
            $mainConfEvent->setSummary("Livecon Conference");
            $mainConfEvent->setIsMainConfEvent(true);
            $mainConfEvent->setStartAt( new \DateTime('now'));
            $end = new \DateTime('now');
            $mainConfEvent->setEndAt( $end->add(new \DateInterval('P2D')));
            $mainConfEvent->addCategorie($categorie);
            $mainConfEvent->setConference($entity);
            $em->persist($mainConfEvent);


            // conference location
            $mainConfEventLocation = new Location();
            $mainConfEventLocation->setName("Conference's location");
            $mainConfEventLocation->addLocationAwareCalendarEntitie($mainConfEvent);
            $mainConfEventLocation->setConference($entity);
            $em->persist($mainConfEventLocation);
            $mainConfEvent->setLocation($mainConfEventLocation);
            $em->persist($mainConfEvent);

            //Create authorization
             $creatorAuthorization = new Authorization();
             $creatorAuthorization->setConference($entity);
             $creatorAuthorization->setUser($user);
             $creatorAuthorization->setFlagApp(1);
             $creatorAuthorization->setFlagSched(1);
             $creatorAuthorization->setFlagconfDatas(1);
             $creatorAuthorization->setFlagTeam(1);
             $em->persist($creatorAuthorization);

            //Linking app config to conference
            $entity->setAppConfig($defaultAppConfig);
            $entity->setMainConfEvent($mainConfEvent);
            $entity->setModule($defaultModule);

            //Add conference to current manager
            $user->setCurrentConf($entity);
            $user->addConference($entity); 

            $em->persist($user);
            $em->persist($entity); 
            $em->flush();

            //Create slug after persist => visibleon endpoint
            $entity->slugify();
            $em->persist($entity); 
            $em->flush();


        return $this->redirect($this->generateUrl('schedule_conference_edit')); 
    }

    
    /**
     * @Route("/removeManager", name="schedule_conference_remove_manager") 
    */
    public function removeManager(Request $request)
    {

        $id = $request->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $manager = $em->getRepository('fibeSecurityBundle:User')->find($id);
        if (!$manager) {
            throw $this->createNotFoundException('Unable to find Manager.');
        }

        $currentConf = $this->getUser()->getCurrentConf();
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') && $this->getUser()->getAuthorizationByConference($currentConf)->getFlagTeam()==1 )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin or team Manager');
        }

        //It must stay one manager in a conference
        if(count($currentConf->getConfManagers())>1){
             //Remove authorization
            $authorization = $currentConf->getAuthorizationByUser($manager);
            $em->remove($authorization);
            //Remove current conf from the user conferences collection
            $manager->removeConference($currentConf);
            $em->persist($manager); 
            $em->flush();

           $this->container->get('session')->getFlashBag()->add(
                  'success',
                  'The manager has been successfully remove from the conferences'
              );
        }else{

            $this->container->get('session')->getFlashBag()->add(
                'error',
                'It must stay unless one manager by conference.'
            );
        }
        
         return $this->redirect($this->generateUrl('conference_team_list'));

    }

      /**
     * @Route("/settings", name="schedule_conference_settings")
     * 
     * @Template()
     */
    public function settingsAction(Request $request)
    {    
        $em = $this->getDoctrine()->getManager();       
        $wwwConf = $this->getUser()->getCurrentConf();
        $module = $wwwConf->getModule();

      
      //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $ModuleForm = $this->createForm(new ModuleType(), $wwwConf->getModule());

    
      /*  if(!$authorization->getFlagconfDatas()){
          throw new AccessDeniedException('Action not authorized !');
        }  */
            
        return array(
              'wwwConf'  => $wwwConf,
              'module' => $module,
              'authorized' => $authorization->getFlagconfDatas(),
              'module_form' => $ModuleForm->createView(),
              );
        
    }


     /**
   * @Route("/{id}/delete", name="schedule_conference_delete") 
   */
    public function deleteAction(Request $request,$id)
    {
      $em = $this->getDoctrine()->getManager();
      $user=$this->getUser();


      $conference = $em->getRepository('fibeWWWConfBundle:WwwConf')->find($id);

      if (!$conference) {
            throw $this->createNotFoundException('Unable to find Conference.');
        }

      if (!$user->authorizedAccesToConference($conference)) {
          throw new AccessDeniedException('Look at your conferences !!!');
      } 

      //Authorization Verification conference datas manager
      $authorization = $user->getAuthorizationByConference($conference);
      if(!$authorization->getFlagconfDatas()){
        throw new AccessDeniedException('Action not authorized !');
      } 
    
      //Change User current Conf
      $user->setCurrentConf(null);
      $em->persist($user);

      //Empty conf datas
      $emptyConf = $this->get('emptyConf');
      $emptyConf->prepareDeleteConf($conference,$em);
      $em->remove($conference);
      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
                'success',
                'conference successfully deleted.'
            );
      return $this->redirect($this->generateUrl('dashboard_index'));
    }


}
