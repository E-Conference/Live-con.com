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
use fibe\Bundle\WWWConfBundle\Entity\Theme;

class databaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wwwconf:database:init')
            ->setDescription('Insert data for a conference management')
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

        // conf
        $conf = new WwwConf(); 
        $conf->setConfName("Conference");
        $em->persist($conf);

        //RoleType
        $roleType = new RoleType(); 
        $roleType->setLibelle("Speaker");
        $em->persist($roleType);

        $roleType = new RoleType(); 
        $roleType->setLibelle("Chair");
        $em->persist($roleType);


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
                  ->setIcon("facetime-video");
        $em->persist($equipment);

        $equipment = new Equipment(); 
        $equipment->setLabel("Microphone")
                  ->setIcon("microphone");
        $em->persist($equipment);

        //Theme 
        $theme = new Theme(); 
        $theme->setLibelle("Business");
        $em->persist($theme);

        $theme = new Theme(); 
        $theme->setLibelle("Design");
        $em->persist($theme);

        $theme = new Theme(); 
        $theme->setLibelle("Marketing");
        $em->persist($theme);

        $theme = new Theme(); 
        $theme->setLibelle("Recherche");
        $em->persist($theme);

        $theme = new Theme(); 
        $theme->setLibelle("Tech");
        $em->persist($theme);


        //categories
        $category = new Category(); 
        $category->setName("Keynote")
                 ->setColor("#afcbe0");
        $em->persist($category);

        $category = new Category(); 
        $category->setName("Tour de table / Blend mix")
                 ->setColor("#e7431e");
        $em->persist($category);

        $category = new Category(); 
        $category->setName("Conference")
                 ->setColor("#b0ca0f");
        $em->persist($category);

        $category = new Category(); 
        $category->setName("Atelier")
                 ->setColor("#e1cc25");
        $em->persist($category);

        $category = new Category(); 
        $category->setName("Speed Demos")
                 ->setColor("#00a2e0");
        $em->persist($category);
        /*

            INSERT INTO `idci_schedule_category` (`id`, `parent_id`, `name`, `slug`, `description`, `color`, `level`, `tree`) VALUES
            (2, NULL, 'ConferenceEvent', 'conferenceevent', NULL, 'lime', 0, NULL),
            (3, NULL, 'KeynoteEvent', 'keynoteevent', NULL, 'red', 0, NULL),
            (4, NULL, 'PanelEvent', 'panelevent', NULL, 'blue', 0, NULL),
            (5, NULL, 'SessionEvent', 'sessionevent', NULL, 'orange', 0, NULL),
            (6, NULL, 'TalkEvent', 'talkevent', NULL, 'gold', 0, NULL),
            (7, NULL, 'TrackEvent', 'trackevent', NULL, 'coral', 0, NULL),
            (8, NULL, 'TutorialEvent', 'tutorialevent', NULL, 'crimson', 0, NULL),
            (*, NULL, 'WorkshopEvent', 'workshopevent', NULL, 'aquamarine', 0, NULL);
        */


        $em->flush();

        $output->writeln("row inserted successfully");
    }
}