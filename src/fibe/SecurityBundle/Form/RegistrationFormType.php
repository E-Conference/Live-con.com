<?php

  namespace fibe\SecurityBundle\Form;

  use Symfony\Component\Form\FormBuilderInterface;
  use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

  /**
   * @TODO comment
   *
   * Class RegistrationFormType
   * @package fibe\SecurityBundle\Form
   */
  class RegistrationFormType extends BaseType
  {
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      parent::buildForm($builder, $options);

      // add your custom field
      $builder
        /*  ->add('roles', 'collection', array(
                   'type' => 'choice',
                   'options' => array(
                        'choices'   => array(
                            'ROLE_ADMIN'   => 'Admin',
                            'ROLE_ADMIN_WWWCONF' => 'Admin WWWConf',
                        )
                   )
              ))*/

        ->add('captcha', 'captcha', array('required' => true));;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return 'fibe_user_registration';
    }
  }