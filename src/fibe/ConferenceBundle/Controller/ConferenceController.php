<?php

  namespace fibe\ConferenceBundle\Controller;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
  use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
  use fibe\Bundle\WWWConfBundle\Entity\Category;
  use fibe\MobileAppBundle\Entity\MobileAppConfig;
  use fibe\Bundle\WWWConfBundle\Form\WwwConfType;
  use fibe\Bundle\WWWConfBundle\Form\ModuleType; 

  use fibe\SecurityBundle\Entity\Team; 

  use fibe\Bundle\WWWConfBundle\Form\XPropertyType;
  use fibe\Bundle\WWWConfBundle\Form\EventType;
  use fibe\Bundle\WWWConfBundle\Entity\XProperty;
  use fibe\Bundle\WWWConfBundle\Entity\Event;
  use fibe\Bundle\WWWConfBundle\Entity\Location;
  use fibe\Bundle\WWWConfBundle\Entity\Module;


  use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;

  /**
   * Link controller.
   *
   * @Route("/admin/conference")
   */
  class ConferenceController extends Controller
  {


    /**
     * @Route("", name="schedule_conference_show")
     *
     * @Template()
     */
    public function showAction(Request $request)
    {
      $conference = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW','WwwConf',$this->getUser()->getCurrentConf());
      
      $mainConfEvent = $conference->getMainConfEvent();
      $form = $this->createForm(new WwwConfType($this->getUser()), $conference);

      $request = $this->get('request');
      if ($request->getMethod() == 'POST')
      {
        $form->bind($request);

        if ($form->isValid())
        {
          $em = $this->getDoctrine()->getManager();
          $conference->slugify();
          $em->persist($conference);
          $conference->uploadLogo();
          $em->flush();

          $this->container->get('session')->getFlashBag()->add(
            'success',
            'The conference has been successfully updated'
          );
        }
        else
        {

          $this->container->get('session')->getFlashBag()->add(
            'error',
            'Submition error, please try again.'
          );
        }
      }

      return array(
        'conference' => $conference,
        'form'       => $form->createView(), 
      );

    } 


    /**
     * @Route("/{id}/empty", name="schedule_conference_empty")
     */
    public function emptyAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();

      $conference = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE','WwwConf',$this->getUser()->getCurrentConf());

      //TODO CSRF TOKEN
      // $csrf = $this->get('form.csrf_provider'); //Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider 
      // $token = $csrf->generateCsrfToken($intention); //Intention should be empty string, if you did not define it in parameters
      // BOOLEAN $csrf->isCsrfTokenValid($intention, $token); 

      $emptyConf = $this->get('emptyConf');
      $emptyConf->emptyConf($conference, $em);

      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
        'success',
        'conference successfully emptied.'
      );
      return $this->redirect($this->generateUrl('schedule_conference_show'));
    }

    /**
     * Creates a new COnference.
     * @Route("/create", name="schedule_conference_create")
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
      $defaultModule->setSponsorModule(1);
      $defaultModule->setConference($entity);
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
      $defaultAppConfig->setLang("EN");

      $em->persist($defaultAppConfig); 

      //Main conf event
      $mainConfEvent = new ConfEvent();
      $mainConfEvent->setSummary("Livecon Conference");
      $mainConfEvent->setIsMainConfEvent(true);
      $mainConfEvent->setStartAt(new \DateTime('now'));
      $end = new \DateTime('now');
      $mainConfEvent->setEndAt($end->add(new \DateInterval('P2D')));
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

      //conference categories


      //categories

      //abstract category

      // $OrganisedEvent = new Category();
      // $OrganisedEvent->setName("OrganisedEvent")
      //          ->setColor("#0EFF74") ;
      // $em->persist($OrganisedEvent);

      // $NonAcademicEvent = new Category();
      // $NonAcademicEvent->setName("NonAcademicEvent")
      //                 ->setColor("#A6FF88")
      //                 ->setParent($OrganisedEvent);
      // $em->persist($NonAcademicEvent);

      // $AcademicEvent = new Category();
      // $AcademicEvent->setName("AcademicEvent")
      //               ->setColor("#57A5C9")
      //               ->setParent($OrganisedEvent);
      // $em->persist($AcademicEvent);

      // non academic

      $SocialEvent = new Category();
      $SocialEvent->setConference($entity)
                    ->setName("Social event")
                    ->setColor("#B186D7")// ->setParent($NonAcademicEvent)
      ;
      $em->persist($SocialEvent);

      $MealEvent = new Category();
      $MealEvent->setConference($entity)
                    ->setName("Meal Event")
                    ->setColor("#00a2e0")// ->setParent($NonAcademicEvent)
      ;
      $em->persist($MealEvent);

      $BreakEvent = new Category();
      $BreakEvent->setConference($entity)
                    ->setName("Break event")
                    ->setColor("#00a2e0")// ->setParent($NonAcademicEvent)
      ;
      $em->persist($BreakEvent);

      // academic

      $KeynoteEvent = new Category();
      $KeynoteEvent->setConference($entity)
                    ->setName("Keynote event")
                    ->setColor("#afcbe0")// ->setParent($AcademicEvent)
      ;
      $em->persist($KeynoteEvent);

      $TrackEvent = new Category();
      $TrackEvent->setConference($entity)
                    ->setName("Track event")
                    ->setColor("#afcbe0")// ->setParent($AcademicEvent)
      ;
      $em->persist($TrackEvent);

      $PanelEvent = new Category();
      $PanelEvent->setConference($entity)
                    ->setName("Panel event")
                    ->setColor("#e7431e")// ->setParent($AcademicEvent)
      ;
      $em->persist($PanelEvent);

      $ConferenceEvent = new Category();
      $ConferenceEvent->setConference($entity)
                    ->setName("Conference event")
                    ->setColor("#b0ca0f")// ->setParent($AcademicEvent)
      ;
      $em->persist($ConferenceEvent);

      $WorkshopEvent = new Category();
      $WorkshopEvent->setConference($entity)
                    ->setName("Workshop event")
                    ->setColor("#EBD94E")// ->setParent($AcademicEvent)
      ;
      $em->persist($WorkshopEvent);

      $SessionEvent = new Category();
      $SessionEvent->setConference($entity)
                    ->setName("Session event")
                    ->setColor("#8F00FF")// ->setParent($AcademicEvent)
      ;
      $em->persist($SessionEvent);

      $TalkEvent = new Category();
      $TalkEvent->setConference($entity)
                    ->setName("Talk event")
                    ->setColor("#FF5A45")// ->setParent($AcademicEvent)
      ;
      $em->persist($TalkEvent);

      //Team
      $defaultTeam = new Team();
      $defaultTeam->addConfManager($user);
      $user->addTeam($defaultTeam);
      $defaultTeam->setConference($entity);
      $entity->setTeam($defaultTeam);
      $em->persist($defaultTeam); 

      //Linking app config to conference
      $entity->setAppConfig($defaultAppConfig);
      $entity->setMainConfEvent($mainConfEvent);
      $entity->setModule($defaultModule);
      $mainConfEvent->addCategorie($ConferenceEvent);

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


      return $this->redirect($this->generateUrl('schedule_conference_show'));
    } 

    /*************************** MODULE *************************/

    /**
     * @Route("/module", name="schedule_conference_module")
     *
     * @Template()
     */
    public function moduleAction(Request $request)
    { 
      $module = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT','Module',$this->getUser()->getCurrentConf()->getModule());

      $moduleForm = $this->createForm(new ModuleType(), $module); 

      return array( 
        'module'      => $module,
        'module_form' => $moduleForm->createView(),
      );

    }


  /**
   * Edits an existing Module entity.
   * @Route("{id}/module", name="schedule_module_update")
   *
   * @param Request $request
   * @param         $id
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function updateModuleAction(Request $request, $id)
  {

    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT','Module',$this->getUser()->getCurrentConf()->getModule());
 
    $editForm = $this->createForm(new ModuleType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
        'success',
        'The module is succesfully updated'
      );
    }
    else
    {

      $this->container->get('session')->getFlashBag()->add(
        'error',
        'The module cannot be saved'
      );
    }

    return $this->redirect($this->generateUrl('schedule_conference_module'));
  }


    /**
     * @Route("/{id}/delete", name="schedule_conference_delete")
     */
    public function deleteAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $user = $this->getUser();

      $conference = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE','WwwConf',$id);

      //Change User current Conf
      $user->setCurrentConf(null);
      $em->persist($user);

      //Empty conf datas
      $emptyConf = $this->get('emptyConf');
      $emptyConf->prepareDeleteConf($conference, $em);
      $em->remove($conference);
      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
        'success',
        'conference successfully deleted.'
      );
      return $this->redirect($this->generateUrl('dashboard_index'));
    }


  }
