<?php

  namespace fibe\Bundle\WWWConfBundle\Form;

  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolverInterface;

  /**
   * Class SponsorType
   * @package fibe\Bundle\WWWConfBundle\Form
   */
  class SponsorType extends AbstractType
  {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('name')
        ->add('logo', 'file', ['required' => false,
                                    'label'    => 'Logo (jpeg - png - 2MO)',
                                    'attr'     => ['placeholder' => 'logoPath']])
        ->add('url', 'url', ['required' => false])
        ->add('description', 'textarea', ['required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Sponsor'
      ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
      return 'fibe_bundle_wwwconfbundle_sponsortype';
    }
  }
