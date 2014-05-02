<?php

  namespace fibe\MobileAppBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  use fibe\Bundle\WWWConfBundle\Form\WwwConfEventType;


  /**
   * Class MobileAppWwwConfType
   * @package fibe\MobileAppBundle\Form
   */
  class MobileAppWwwConfType extends AbstractType
  {
    private $user;

    /**
     * @param $user
     * @param $entity
     */
    public function __construct($user, $entity)
    {
      $this->user = $user;
      $this->entity = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('logo', 'file', array('required' => false,
                                    'label'    => 'Logo (jpeg - png - 2MO)',
                                    'attr'     => array('placeholder' => 'logoPath')))
        ->add('mainConfEvent', new MobileAppWwwConfEventType($this->user, $this->entity), array(
          'label' => 'Conference event',
          'attr'  => array('class' => 'well')));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class'         => 'fibe\Bundle\WWWConfBundle\Entity\WwwConf',
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
      return 'fibe_bundle_wwwconfbundle_wwwconftype';
    }
  }
