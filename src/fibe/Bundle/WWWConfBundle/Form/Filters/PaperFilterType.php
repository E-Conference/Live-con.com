<?php

  namespace fibe\Bundle\WWWConfBundle\Form\Filters;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * @TODO comment
   *
   * Class PaperFilterType
   * @package fibe\Bundle\WWWConfBundle\Form\Filters
   */
  class PaperFilterType extends AbstractType
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
          'class'    => 'fibeWWWConfBundle:Paper',
          'label'    => 'Title',
          'choices'  => $this->user->getCurrentConf()->getPapers()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Title')
        ))
        ->add('author', 'entity', array(
          'class'    => 'fibeWWWConfBundle:Person',
          'label'    => 'Author',
          'choices'  => $this->user->getCurrentConf()->getPersons()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Author')
        ))
        ->add('topic', 'entity', array(
          'class'    => 'fibeWWWConfBundle:Topic',
          'label'    => 'Subject',
          'choices'  => $this->user->getCurrentConf()->getTopics()->toArray(),
          'required' => false,
          'attr'     => array('placeholder' => 'Topic')

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
      return 'fibe_bundle_wwwconfbundle_paperfiltertype';
    }
  }
