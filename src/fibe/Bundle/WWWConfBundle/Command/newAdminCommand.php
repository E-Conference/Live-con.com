<?php 

namespace fibe\Bundle\WWWConfBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
use fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig;

use IDCI\Bundle\SimpleScheduleBundle\Entity\Location;

use FOS\UserBundle\Model\User;

class newAdminCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('livecon:admin:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>fos:user:create</info> command creates a user:

  <info>php app/console fos:user:create matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console fos:user:create matthieu matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console fos:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console fos:user:create thibault --inactive</info>

EOT
            );
    }


   
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $newUser = $manipulator->create($username, $password, $email, !$inactive, $superadmin);

        $em = $this->getContainer()->get('doctrine')->getManager('default');
        //Create the default conference
        $defaultConference = new WwwConf();
        $defaultConference->setLogoPath("livecon.png");
        $defaultConference->setAcronym("My conference");
        $em->persist($defaultConference);

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
       
       


        $em->persist($defaultAppConfig);

        $categorie = $em->getRepository('IDCISimpleScheduleBundle:Category')->findOneBySlug("conferenceevent");
        
        //Main conf event  
        $mainConfEvent = new ConfEvent();
        $mainConfEvent->setSummary("Conference");
        $mainConfEvent->setStartAt( new \DateTime('now'));
        $mainConfEvent->setEndAt( new \DateTime('now'));
        $mainConfEvent->addCategorie($categorie);
        $mainConfEvent->setSummary("Conference Event");
        $mainConfEvent->setConference($defaultConference);

        // conference location
        $mainConfEventLocation = new Location();
        $mainConfEventLocation->setName("Conference's location");
        $mainConfEventLocation->addLocationAwareCalendarEntitie($mainConfEvent);
        $em->persist($mainConfEventLocation);
 
        $em->persist($mainConfEvent);

        //Linking app config to conference
        $defaultConference->setAppConfig($defaultAppConfig);
        $defaultConference->setMainConfEvent($mainConfEvent);
        $em->persist($defaultConference);

        //Join the new user with his default conference
        $newUser->addConference($defaultConference);
        $newUser->setCurrentConf($defaultConference);

        $em->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}