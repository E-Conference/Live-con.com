<?php 
namespace fibe\Bundle\WWWConfBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use IDCI\Bundle\SimpleScheduleBundle\Entity\Status;
use IDCI\Bundle\SimpleScheduleBundle\Entity\Category;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;
use fibe\Bundle\WWWConfBundle\Entity\Equipment;
use fibe\Bundle\WWWConfBundle\Entity\Topic;
use fibe\Bundle\WWWConfBundle\Entity\SocialService;

class databaseInitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wwwconf:database:init')
            ->setDescription('Insert data for conference managment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager('default');

        $status = new Status();
        $status->setValue('TENTATIVE')
               ->setDiscr('TENTATIVE');
        $em->persist($status);

        $status = new Status();
        $status->setValue('CONFIRMED')
               ->setDiscr('CONFIRMED'); 
        $em->persist($status);

        $status = new Status();
        $status->setValue('CANCELLED')
               ->setDiscr('CANCELLED'); 
        $em->persist($status);
 

        //RoleType
        $roleType = new RoleType(); 
        $roleType->setName("Delegate");
        $roleType->setLabel("Delegate");
        $em->persist($roleType);

        $roleType = new RoleType(); 
        $roleType->setName("Chair");
        $roleType->setLabel("Chair");
        $em->persist($roleType);

        $roleType = new RoleType(); 
        $roleType->setName("Presenter");
        $roleType->setLabel("Presenter");
        $em->persist($roleType);

        $roleType = new RoleType(); 
        $roleType->setName("ProgrammeCommitteeMember");
        $roleType->setLabel("Programme Committee Member");
        $em->persist($roleType);

        //Social Service 
        $socialService = new SocialService(); 
        $socialService->setName("Facebook");
        $em->persist($socialService);

        $socialService = new SocialService(); 
        $socialService->setName("Twitter");
        $em->persist($socialService);

        $socialService = new SocialService(); 
        $socialService->setName("LinkedIn");
        $em->persist($socialService);

        //Equipments
        $equipment = new Equipment(); 
        $equipment->setLabel("Computer")
                  ->setIcon("laptop");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("Speaker")
                  ->setIcon("volume-up");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("Wifi")
                  ->setIcon("rss");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("Screen")
                  ->setIcon("film");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("OHP")
                  ->setIcon("video-camera");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("Microphone")
                  ->setIcon("microphone");
        $em->persist($equipment);

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
        $SocialEvent = new Category(); 
        $SocialEvent->setName("SocialEvent")
                    ->setColor("#B186D7")
                    // ->setParent($NonAcademicEvent)
                    ;
        $em->persist($SocialEvent);
         
        $MealEvent = new Category(); 
        $MealEvent->setName("MealEvent")
                  ->setColor("#00a2e0")
                  // ->setParent($NonAcademicEvent)
                  ;
        $em->persist($MealEvent);
        
        $BreakEvent = new Category(); 
        $BreakEvent->setName("BreakEvent")
                  ->setColor("#00a2e0")
                  // ->setParent($NonAcademicEvent)
                  ;
        $em->persist($BreakEvent); 

        // academic

        $KeynoteEvent = new Category();
        $KeynoteEvent->setName("KeynoteEvent")
                 ->setColor("#afcbe0")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($KeynoteEvent);

        $TrackEvent = new Category();
        $TrackEvent->setName("TrackEvent")
                  ->setColor("#afcbe0")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($TrackEvent);

        $PanelEvent = new Category(); 
        $PanelEvent->setName("PanelEvent")
                  ->setColor("#e7431e")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($PanelEvent);

        $ConferenceEvent = new Category(); 
        $ConferenceEvent->setName("ConferenceEvent")
                  ->setColor("#b0ca0f")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($ConferenceEvent);

        $WorkshopEvent = new Category(); 
        $WorkshopEvent->setName("WorkshopEvent")
                  ->setColor("#EBD94E")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($WorkshopEvent);

        $SessionEvent = new Category(); 
        $SessionEvent->setName("SessionEvent")
                  ->setColor("#8F00FF")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($SessionEvent);

        $TalkEvent = new Category(); 
        $TalkEvent->setName("TalkEvent")
                  ->setColor("#FF5A45")
                  // ->setParent($AcademicEvent)
                  ;
        $em->persist($TalkEvent);



        $em->flush();

        $output->writeln("rows inserted successfully");
    }
}