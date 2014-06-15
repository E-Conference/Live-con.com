<?php
  namespace fibe\Bundle\WWWConfBundle\Command;

  use fibe\Bundle\WWWConfBundle\Entity\Equipment;
  use fibe\Bundle\WWWConfBundle\Entity\RoleType;
  use fibe\Bundle\WWWConfBundle\Entity\SocialService;
  use fibe\Bundle\WWWConfBundle\Entity\Category;
  use fibe\Bundle\WWWConfBundle\Entity\Status;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;

  /**
   * Initialization command for the DataBase
   *
   * Class databaseInitCommand
   * @package fibe\Bundle\WWWConfBundle\Command
   */
  class databaseInitCommand extends ContainerAwareCommand
  {

    protected function configure()
    {
      $this
        ->setName('wwwconf:database:init')
        ->setDescription('Insert data for conference managment');
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



      $em->flush();

      $output->writeln("rows inserted successfully");
    }
  }
