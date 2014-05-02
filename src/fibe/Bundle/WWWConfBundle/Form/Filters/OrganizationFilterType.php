<?php

  namespace fibe\Bundle\WWWConfBundle\Form\Filters;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * @TODO comment
   *
   * Class OrganizationFilterType
   * @package fibe\Bundle\WWWConfBundle\Form\Filters
   */
  class OrganizationFilterType extends AbstractType
  {

    private $user;

    /**
     * Constructor
     *
     * @param $user
     */
    public function __construct($user)
    {
      $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('id', 'entity', array(
          'class'    => 'fibeWWWConfBundle:Organization',
          'label'    => 'Name',
          'choices'  => $this->user->getCurrentConf()->getOrganizations()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Name')
        ))
        ->add('member', 'entity', array(
          'class'    => 'fibeWWWConfBundle:Person',
          'label'    => 'Member',
          'choices'  => $this->user->getCurrentConf()->getPersons()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Member')
        ))
        ->add('country', 'text', array(
          'label'    => 'Country',
          'required' => false,
          'attr'     => array('placeholder' => 'Country')
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'csrf_protection'   => false,
        'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_organizationfiltertype';
    }
  }
