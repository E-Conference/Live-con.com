<?php

  namespace fibe\SecurityBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;
  use Symfony\Component\Security\Acl\Permission\MaskBuilder;
  use fibe\SecurityBundle\Services\ACLHelper;

  class UserConfPermissionType extends AbstractType
  {
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder 
        ->add('user', 'entity', array(
          'class' => 'fibeSecurityBundle:User',
          'property' => 'username',
          'required'  => true,
          )) 
        ->add('confPermissions',  'collection', array( 
          'type'   => new ConfPermissionType(), 
          'label'   => 'Permissions for the user : ', 
          'allow_add' => true,
          'options'  => array(
            'data_class' => 'fibe\SecurityBundle\Entity\ConfPermission',
            'required'  => true,
          ))
        )
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