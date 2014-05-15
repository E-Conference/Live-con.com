<?php

  namespace fibe\SecurityBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Form\FormEvent;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;
  use Symfony\Component\Security\Acl\Permission\MaskBuilder;
  use fibe\SecurityBundle\Services\ACLHelper;
  use Doctrine\ORM\EntityRepository;
  use Symfony\Component\Form\FormEvents;

  class UserConfPermissionType extends AbstractType
  {
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder 
        // ->add('user', 'entity', array(
        //   'class' => 'fibeSecurityBundle:User',
        //   'property' => 'username',
        //   'required'  => true,
        //   )) 
        ->add('confPermissions',  'collection', array( 
          'type'   => new ConfPermissionType(), 
          'label'   => 'Permissions for the user : ', 
          'allow_add' => true,
          'options'  => array(
            'data_class' => 'fibe\SecurityBundle\Entity\ConfPermission',
            'required'  => true,
          ))
        );

        //supprime les membres de la team 
        $user = $this->user;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($user) {
                $form = $event->getForm();
 
                $form->add('user', 'entity', array(
                    'class' => 'fibeSecurityBundle:User',
                    'property' => 'username',
                    'required'  => true,
                    'query_builder' => function(EntityRepository $er) use ($user) {
                      return $er->ManagerForSelectTeamQuery($user->getTeams()); 
                    },
                ));
            }
        );
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return 'fibe_user_conf_permission';
    }
  }