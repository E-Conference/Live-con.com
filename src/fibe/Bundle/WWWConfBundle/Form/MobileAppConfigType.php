<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * Class MobileAppConfigType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class MobileAppConfigType extends AbstractType
  {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder

        ->add('BGColorHeader', 'text', array(
          'required' => true,
          'label'    => 'Header background',
          'attr'     => array('class' => 'color form-control BGColorHeader')
        ))
        ->add('TitleColorHeader', 'text', array(
          'required' => true,
          'label'    => 'Header titles',
          'attr'     => array('class' => 'color form-control TitleColorHeader')
        ))
        ->add('BGColorNavBar', 'text', array(
          'required' => true,
          'label'    => 'Nav barre background',
          'attr'     => array('class' => 'color form-control BGColorNavBar')
        ))
        ->add('TitleColorNavBar', 'text', array(
          'required' => true,
          'label'    => 'Nav Barre titles',
          'attr'     => array('class' => 'color form-control TitleColorNavBar')
        ))
        ->add('BGColorContent', 'text', array(
          'required' => true,
          'label'    => 'Content background',
          'attr'     => array('class' => 'color form-control BGColorContent')
        ))
        ->add('TitleColorContent', 'text', array(
          'required' => true,
          'label'    => 'Content titles',
          'attr'     => array('class' => 'color form-control TitleColorContent')
        ))
        ->add('BGColorButton', 'text', array(
          'required' => true,
          'label'    => 'Button background',
          'attr'     => array('class' => 'color form-control BGColorButton')
        ))
        ->add('TitleColorButton', 'text', array(
          'required' => true,
          'label'    => 'Button text',
          'attr'     => array('class' => 'color form-control TitleColorButton')
        ))
        ->add('BGColorFooter', 'text', array(
          'required' => true,
          'label'    => 'Footer background',
          'attr'     => array('class' => 'color form-control BGColorFooter')
        ))
        ->add('TitleColorFooter', 'text', array(
          'required' => true,
          'label'    => 'Footer titles',
          'attr'     => array('class' => 'color form-control TitleColorFooter')
        ))
        ->add('googleDatasource', 'checkbox', array(
          'required' => false,
          'label'    => 'activated :',
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          ),
        ))
        ->add('duckduckgoDatasource', 'checkbox', array(
          'required' => false,
          'label'    => 'activated :',
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          ),
        ))
        ->add('dblpDatasource', 'checkbox', array(
          'required' => false,
          'label'    => 'activated :',
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          )
        ))
        ->add('lang', 'text', array(
          'required' => true,
          'label'    => 'Language (FR / EN)',
          'attr'     => array('class' => 'color form-control')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\MobileAppBundle\Entity\MobileAppConfig'
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_mobileappconfigtype';
    }
  }
