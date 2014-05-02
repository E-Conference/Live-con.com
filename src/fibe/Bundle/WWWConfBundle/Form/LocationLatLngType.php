<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;


  /**
   * Class LocationLatLngType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class LocationLatLngType extends AbstractType
  {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('name')
        ->add('latitude')
        ->add('longitude');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Location',
        'cascade_validation' => true,
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_locationlatlngType';
    }
  }
