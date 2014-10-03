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

class UnshareRoleTypesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wwwconf:database:unshare:roletypes')
            ->setDescription('Stop sharing roletypes between conferences')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager('default');

        $conferences = $em->getRepository('fibeWWWConfBundle:WwwConf')->findAll();

        foreach($conferences as $conference)
        {
            foreach($conference->getRoles() as $role)
            {
                $roleType = $role->getType();
                $roleType->setConference($conference);
                $em->persist($roleType);
            }

        }

        $em->flush();

        $output->writeln("rows inserted successfully");
    }
}