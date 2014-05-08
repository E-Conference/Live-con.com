<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * Class ModuleType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class ModuleType extends AbstractType
  {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('paperModule', 'checkbox', array(
          'label'    => 'Publications ',
          'required' => false,
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          ),
        ))
        ->add('organizationModule', 'checkbox', array(
          'label'    => 'Organizations ',
          'required' => false,
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          ),
        ))
        ->add('sponsorModule', 'checkbox', array(
          'label'    => 'Sponsors ',
          'required' => false,
          'attr'     => array('class'          => 'switch switch-small',
                              'data-on-label'  => "<i class='fa fa-check fa-white'>",
                              'data-off-label' => "<i class='fa fa-ban'>",

          ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Module'
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_moduletype';
    }
  }
