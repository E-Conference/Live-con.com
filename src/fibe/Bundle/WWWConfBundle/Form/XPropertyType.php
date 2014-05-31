<?php

  /**
   *
   * @author :  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
   * @licence: GPL
   *
   */

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * Class XPropertyType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class XPropertyType extends AbstractType
  {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('xnamespace', 'choice', array(
          'label'   => 'Link type',
          'choices' => array('publication_uri' => 'publication',
                             'event_uri'       => 'event')))
        ->add('xkey', null, array('label' => 'Name'))
        ->add('xvalue', null, array('label' => 'Uri'))
        ->add('calendarEntity', null, array(
          'label' => ' ',
          'attr'  => array('style' => 'display:none')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\XProperty'
      ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_xpropertytype';
    }
  }
