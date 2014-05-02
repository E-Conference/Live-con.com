<?php

  namespace fibe\Bundle\WWWConfBundle\Form\Filters;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;
  use Doctrine\ORM\EntityRepository;


  /**
   * @TODO comment
   *
   * Class ConfEventFilterType
   * @package fibe\Bundle\WWWConfBundle\Form\Filters
   */
  class ConfEventFilterType extends AbstractType
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
        ->add('summary', 'entity', array(
          'class'    => 'fibeWWWConfBundle:ConfEvent',
          'label'    => 'Name',
          'choices'  => $this->user->getCurrentConf()->getEvents()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Summary')
        ))
        ->add('location', 'entity', array(
          'class'      => 'fibeWWWConfBundle:Location',
          'label'      => 'Location',
          'choices'    => $this->user->getCurrentConf()->getLocations()->toArray(),
          'empty_data' => null,
          'required'   => false,
          'attr'       => array('placeholder' => 'Location')
        ))
        ->add('category', 'entity', array(
          'class'         => 'fibeWWWConfBundle:Category',
          'label'         => 'Category',
          'query_builder' => function (EntityRepository $er)
            {
              return $er->extractQueryBuilder(array());
            },
          'empty_data'    => null,
          'required'      => false,
          'attr'          => array('placeholder' => 'Category')
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
      return 'fibe_bundle_wwwconfbundle_confeventfiltertype';
    }
  }
