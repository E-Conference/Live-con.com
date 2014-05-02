<?php

  namespace fibe\MobileAppBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  use fibe\Bundle\WWWConfBundle\Form\WwwConfEventType;

  /**
   * @TODO comment
   *
   * Class MobileAppWwwConfEventType
   * @package fibe\MobileAppBundle\Form
   */
  class MobileAppWwwConfEventType extends WwwConfEventType
  {
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      parent::buildForm($builder, $options);
      $builder
        ->remove('location')
        ->add('acronym', 'text', array('required' => false,
                                       'label'    => 'Acronym',
                                       'attr'     => array('placeholder' => 'Acronym')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\ConfEvent'
      ));
    }

    /**
     * @return string
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_confeventtype';
    }
  }
