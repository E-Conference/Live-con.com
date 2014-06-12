<?php
  namespace fibe\Bundle\WWWConfBundle\Command;

  use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
  use fibe\MobileAppBundle\Entity\MobileAppConfig;
  use fibe\Bundle\WWWConfBundle\Entity\Module;
  use fibe\Bundle\WWWConfBundle\Entity\Organization;
  use fibe\Bundle\WWWConfBundle\Entity\Paper;
  use fibe\Bundle\WWWConfBundle\Entity\Person;
  use fibe\Bundle\WWWConfBundle\Entity\Role;
  use fibe\Bundle\WWWConfBundle\Entity\RoleType;
  use fibe\Bundle\WWWConfBundle\Entity\Topic;
  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
  use fibe\Bundle\WWWConfBundle\Entity\Location;
  use fibe\Bundle\WWWConfBundle\Form\WwwConfType;
  use fibe\Bundle\WWWConfBundle\Form\WwwConfEventType;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;


  /**
   * Initialization command for filling the DataBase
   *
   * Class fillDatabaseUpCommand
   * @package fibe\Bundle\WWWConfBundle\Command
   */
  class fillDatabaseUpCommand extends ContainerAwareCommand
  {
    protected function configure()
    {
      $this
        ->setName('livecon:database:full')
        ->setDescription('Insert lots of data in the database');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

      $em = $this->getContainer()->get('doctrine')->getManager('default');


      //RoleType
      $roleType = new RoleType();
      $roleType->setName("Delegate");
      $roleType->setLabel("Delegate");
      $em->persist($roleType);

      $roleTypeChair = new RoleType();
      $roleTypeChair->setName("Chair");
      $roleTypeChair->setLabel("Chair");
      $em->persist($roleTypeChair);

      $roleTypePresenter = new RoleType();
      $roleTypePresenter->setName("Presenter");
      $roleTypePresenter->setLabel("Presenter");
      $em->persist($roleTypePresenter);

      $roleType = new RoleType();
      $roleType->setName("ProgrammeCommitteeMember");
      $roleType->setLabel("Programme Committee Member");
      $em->persist($roleType);

      //Social Service
      // $socialService = new SocialService();
      // $socialService->setName("Facebook");
      // $em->persist($socialService);

      // $socialService = new SocialService();
      // $socialService->setName("Twitter");
      // $em->persist($socialService);

      // $socialService = new SocialService();
      // $socialService->setName("LinkedIn");
      // $em->persist($socialService);

      // //Equipments
      // $equipment = new Equipment();
      // $equipment->setLabel("Computer")
      //           ->setIcon("laptop");
      // $em->persist($equipment);

      // $equipment = new Equipment();
      // $equipment->setLabel("Speaker")
      //           ->setIcon("volume-up");
      // $em->persist($equipment);

      // $equipment = new Equipment();
      // $equipment->setLabel("Wifi")
      //           ->setIcon("rss");
      // $em->persist($equipment);

      // $equipment = new Equipment();
      // $equipment->setLabel("Screen")
      //           ->setIcon("film");
      // $em->persist($equipment);

      // $equipment = new Equipment();
      // $equipment->setLabel("OHP")
      //           ->setIcon("video-camera");
      // $em->persist($equipment);

      // $equipment = new Equipment();
      // $equipment->setLabel("Microphone")
      //           ->setIcon("microphone");
      // $em->persist($equipment);

      //Topic
      /* $topic = new Topic();
       $topic->setName("Business");
       $em->persist($topic);

       $topic = new Topic();
       $topic->setName("Design");
       $em->persist($topic);

       $topic = new Topic();
       $topic->setName("Marketing");
       $em->persist($topic);

       $topic = new Topic();
       $topic->setName("Recherche");
       $em->persist($topic);

       $topic = new Topic();
       $topic->setName("Tech");
       $em->persist($topic);*/


      //categories
      //TODO : color

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
      // $SocialEvent = new Category();
      // $SocialEvent->setName("SocialEvent");
      //  $SocialEvent->setLabel("Social event")
      //              ->setColor("#B186D7")
      //             // ->setParent($NonAcademicEvent)
      //             ;
      // $em->persist($SocialEvent);

      // $MealEvent = new Category();
      // $MealEvent->setName("MealEvent");
      // $MealEvent->setLabel("Meal Event")
      //           ->setColor("#00a2e0")
      //           // ->setParent($NonAcademicEvent)
      //           ;
      // $em->persist($MealEvent);

      // $BreakEvent = new Category();
      // $BreakEvent->setName("BreakEvent");
      // $BreakEvent->setLabel("Break event")
      //           ->setColor("#00a2e0")
      //           // ->setParent($NonAcademicEvent)
      //           ;
      // $em->persist($BreakEvent);

      // // academic

      // $KeynoteEvent = new Category();
      // $KeynoteEvent->setName("KeynoteEvent");
      // $KeynoteEvent->setLabel("Keynote event")
      //          ->setColor("#afcbe0")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($KeynoteEvent);

      // $TrackEvent = new Category();
      // $TrackEvent->setName("TrackEvent");
      // $TrackEvent->setLabel("Track event")
      //           ->setColor("#afcbe0")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($TrackEvent);

      // $PanelEvent = new Category();
      // $PanelEvent->setName("PanelEvent");
      // $PanelEvent->setLabel("Panel event")
      //           ->setColor("#e7431e")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($PanelEvent);

      // $ConferenceEvent = new Category();
      // $ConferenceEvent->setName("ConferenceEvent");
      // $ConferenceEvent->setLabel("Conference event")
      //           ->setColor("#b0ca0f")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($ConferenceEvent);

      // $WorkshopEvent = new Category();
      // $WorkshopEvent->setName("WorkshopEvent");
      //  $WorkshopEvent->setLabel("Workshop event")
      //           ->setColor("#EBD94E")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($WorkshopEvent);

      // $SessionEvent = new Category();
      // $SessionEvent->setName("SessionEvent");
      //  $SessionEvent->setLabel("Session event")
      //           ->setColor("#8F00FF")
      //           // ->setParent($AcademicEvent)
      //           ;
      // $em->persist($SessionEvent);

      // $TalkEvent = new Category();
      // $TalkEvent->setName("TalkEvent");
      // $TalkEvent->setLabel("Talk event")
      //           ->setColor("#FF5A45")
      // ->setParent($AcademicEvent)
      ;
      // $em->persist($TalkEvent);
      // $em->flush();

      $output->writeln("common rows inserted successfully");

      for ($counter = 0; $counter <= 1; $counter += 1)
      {
        // get a fresh EM
        $container = $this->getContainer();
        $container->set('doctrine.orm.default', null);
        $container->set('doctrine.orm.entity_manager', null);
        $container->set('doctrine.orm.default_entity_manager', null);
        $this->createConf($counter, 3000, $output, $roleType, $roleTypeChair, $roleTypePresenter);
      }

    }

    /**
     * Create a conference in the database
     *
     * @param $counter           @TODO comment
     * @param $limit             @TODO comment
     * @param $output            @TODO comment
     * @param $roleType          @TODO comment
     * @param $roleTypeChair     @TODO comment
     * @param $roleTypePresenter @TODO comment
     */
    function createConf($counter, $limit, $output, $roleType, $roleTypeChair, $roleTypePresenter)
    {

      $output->writeln("conference " . $counter . " started");
      $em = $this->getContainer()->get('doctrine')->getManager('default');
      //Create the default conference
      $conference = new WwwConf();
      $conference->setLogoPath("livecon.png");
      $em->persist($conference);

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
      $defaultAppConfig->setLang("EN");

      $em->persist($defaultAppConfig);


      //Main conf event
      $mainConfEvent = new ConfEvent();
      $mainConfEvent->setSummary("Big Livecon Conference" . $counter);
      $mainConfEvent->setIsMainConfEvent(true);
      $mainConfEvent->setStartAt(new \DateTime('now'));
      $end = new \DateTime('now');
      $mainConfEvent->setEndAt($end->add(new \DateInterval('P2D')));
      $mainConfEvent->setConference($conference);
      $mainConfEvent->setComment("Livecon Conference " . $counter . " comment");
      $mainConfEvent->setUrl("http://liveconconference" . $counter);
      $em->persist($mainConfEvent);


      // conference location
      $mainConfEventLocation = new Location();
      $mainConfEventLocation->setName("Conference's location");
      $mainConfEventLocation->addLocationAwareCalendarEntitie($mainConfEvent);
      $mainConfEventLocation->setConference($conference);
      $em->persist($mainConfEventLocation);
      $mainConfEvent->setLocation($mainConfEventLocation);
      $em->persist($mainConfEvent);

      //Create authorization
      $creatorAuthorization = new Authorization();
      $creatorAuthorization->setConference($conference);
      $creatorAuthorization->setFlagApp(1);
      $creatorAuthorization->setFlagSched(1);
      $creatorAuthorization->setFlagconfDatas(1);
      $creatorAuthorization->setFlagTeam(1);
      $em->persist($creatorAuthorization);

      //Linking app config to conference
      $conference->setAppConfig($defaultAppConfig);
      $conference->setMainConfEvent($mainConfEvent);
      $conference->setModule($defaultModule);

      //Add conference to current manager


      $em->persist($conference);


      //Create slug after persist => visibleon endpoint
      $conference->slugify();
      $em->persist($conference);

      $location = null;

      for ($counterLoc = 0; $counterLoc <= $limit / 10; $counterLoc += 1)
      {

        $location = new Location();
        $location->setName("location" . $counterLoc);
        $em->persist($location);
      }
      $em->flush();

      for ($counterEnt = 0; $counterEnt <= $limit; $counterEnt += 1)
      {
        $person = new Person();
        $person->setConference($conference);
        $person->setFamilyName("person" . $counterEnt);
        $person->setFirstName("person" . $counterEnt);
        $person->setName("person" . $counterEnt);
        $person->setDescription("person " . $counterEnt . " description descriptiondescription description description description description description description description description description description ");
        $person->setImg("http://png-4.findicons.com/files/icons/61/dragon_soft/128/user.png");
        $person->setEmail("email@lol.fr");
        $person->setPage("mypersonnalpage.com");


        $organization = new Organization();
        $organization->setConference($conference);
        $organization->setName("organization" . $counterEnt);
        $organization->setPage("organization page" . $counterEnt);
        $organization->setCountry("organization country" . $counterEnt);
        $person->addOrganization($organization);
        $organization->addMember($person);

        $topic = new Topic();
        $topic->setName("topic" . $counterEnt);
        $topic->setConference($conference);

        $paper = new Paper();
        $paper->setConference($conference);
        $paper->setTitle("paper" . $counterEnt);
        $paper->setUrl("paper url" . $counterEnt);
        $paper->setAbstract("paper" . $counterEnt . "abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact abstact ");
        $paper->addTopic($topic);
        $paper->addAuthor($person);

        $event = new ConfEvent();
        $event->setConference($conference);
        $event->setStartAt(new \DateTime('now'));
        $end = new \DateTime('now');
        $event->setEndAt($end->add(new \DateInterval('P2D')));
        $event->setSummary("event " . $counterEnt);
        $event->setAttach("event attach" . $counterEnt);
        $event->setDescription("event " . $counterEnt . " description : description description description description description description description description description description description description ");
        $event->addPaper($paper);
        $event->setLocation($location);

        $role = new Role();
        $role->setConference($conference);
        $role->setPerson($person);
        $role->setEvent($event);
        $role->setType($roleTypePresenter);


        $em->persist($roleTypePresenter);
        $em->persist($organization);
        $em->persist($paper);
        $em->persist($topic);
        $em->persist($role);
        $em->persist($event);
        $em->persist($person);

      }
      $em->flush();
      $em->close();
    }
  }
