<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * Class OrganizationType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class OrganizationType extends AbstractType
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
        ->add('name')
        ->add('page', 'text', array('required' => false, 'label' => 'Homepage'))
        ->add('country')
        ->add('members', 'entity', array(
          'class'    => 'fibeWWWConfBundle:Person',
          'label'    => 'Members',
          'multiple' => true,
          'choices'  => $this->user->getCurrentConf()->getPersons()->toArray(),
          'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Organization',
        'csrf_protection' => false
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_organizationtype';
    }
  }
