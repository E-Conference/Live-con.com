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

class UnshareCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wwwconf:database:unshare:categories')
            ->setDescription('Stop sharing categories between conferences')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager('default');

        $conferences = $em->getRepository('fibeWWWConfBundle:WwwConf')->findAll();

        foreach($conferences as $conference)
        {
            $SocialEvent = new Category();
            $SocialEvent->setName("SocialEvent");
            $SocialEvent->setLabel("Social event")
              ->setColor("#B186D7")
              ->setConference($conference)
              // ->setParent($NonAcademicEvent)
            ;
            $em->persist($SocialEvent);

            $MealEvent = new Category();
            $MealEvent->setName("MealEvent");
            $MealEvent->setLabel("Meal Event")
              ->setColor("#00a2e0")
              ->setConference($conference)
              // ->setParent($NonAcademicEvent)
            ;
            $em->persist($MealEvent);

            $BreakEvent = new Category();
            $BreakEvent->setName("BreakEvent");
            $BreakEvent->setLabel("Break event")
              ->setColor("#00a2e0")
              ->setConference($conference)
              // ->setParent($NonAcademicEvent)
            ;
            $em->persist($BreakEvent);

            // academic

            $KeynoteEvent = new Category();
            $KeynoteEvent->setName("KeynoteEvent");
            $KeynoteEvent->setLabel("Keynote event")
              ->setColor("#afcbe0")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($KeynoteEvent);

            $TrackEvent = new Category();
            $TrackEvent->setName("TrackEvent");
            $TrackEvent->setLabel("Track event")
              ->setColor("#afcbe0")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($TrackEvent);

            $PanelEvent = new Category();
            $PanelEvent->setName("PanelEvent");
            $PanelEvent->setLabel("Panel event")
              ->setColor("#e7431e")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($PanelEvent);

            $ConferenceEvent = new Category();
            $ConferenceEvent->setName("ConferenceEvent");
            $ConferenceEvent->setLabel("Conference event")
              ->setColor("#b0ca0f")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($ConferenceEvent);

            $WorkshopEvent = new Category();
            $WorkshopEvent->setName("WorkshopEvent");
            $WorkshopEvent->setLabel("Workshop event")
              ->setColor("#EBD94E")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($WorkshopEvent);

            $SessionEvent = new Category();
            $SessionEvent->setName("SessionEvent");
            $SessionEvent->setLabel("Session event")
              ->setColor("#8F00FF")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($SessionEvent);

            $TalkEvent = new Category();
            $TalkEvent->setName("TalkEvent");
            $TalkEvent->setLabel("Talk event")
              ->setColor("#FF5A45")
              ->setConference($conference)
              // ->setParent($AcademicEvent)
            ;
            $em->persist($TalkEvent);

            foreach($conference->getEvents() as $event)
            {
                foreach($event->getCategories() as $category)
                {
                    $newCat = null;
                    switch($category->getName())
                    {
                        case "TalkEvent":
                          $newCat = $TalkEvent;
                          break;
                        case "SessionEvent":
                          $newCat = $SessionEvent;
                          break;
                        case "WorkshopEvent":
                          $newCat = $WorkshopEvent;
                          break;
                        case "ConferenceEvent":
                          $newCat = $ConferenceEvent;
                          break;
                        case "PanelEvent":
                          $newCat = $PanelEvent;
                          break;
                        case "TrackEvent":
                          $newCat = $TrackEvent;
                          break;
                        case "KeynoteEvent":
                          $newCat = $KeynoteEvent;
                          break;
                        case "BreakEvent":
                          $newCat = $BreakEvent;
                          break;
                        case "MealEvent":
                          $newCat = $MealEvent;
                          break;
                        case "SocialEvent":
                          $newCat = $SocialEvent;
                          break;
                    }
                    if(null !== $newCat)
                    {
                        $event->removeCategorie($category);
                        $event->addCategorie($newCat);
                        $em->persist($event);
                    }
                }
            }

        }




        $em->flush();

        $output->writeln("rows inserted successfully");
    }
}