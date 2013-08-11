<?php 
namespace fibe\Bundle\WWWConfBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use IDCI\Bundle\SimpleScheduleBundle\Entity\Status;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

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
        $em->persist($conf);

        //TODO category
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