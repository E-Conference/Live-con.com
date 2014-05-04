<?php

  namespace fibe\SecurityBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  use fibe\Bundle\WWWConfBundle\Form\WwwConfType;

  /**
   * @TODO comment
   *
   * Class ConfManagerType
   * @package fibe\SecurityBundle\Form
   */
  class ConfManagerType extends AbstractType
  {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('conference', 'collection', array('type'         => new WwwConfType(),
                                                'allow_add'    => true,
                                                'allow_delete' => true));
    }

    *
     * {@inheritdoc}
     
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\SecurityBundle\Entity\User'
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_securitybundle_confmanagertype';
    }
  }
