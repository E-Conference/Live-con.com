<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;


  /**
   * Class WwwConfEventType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class WwwConfEventType extends ConfEventType
  { 

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      parent::buildForm($builder, $options);
      $builder
        ->remove('categories') 
        ->add('location', new LocationLatLngType(), array(
          'label' => 'Conference location (click on the map)',
          'attr'  => array('class' => 'well')));
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
