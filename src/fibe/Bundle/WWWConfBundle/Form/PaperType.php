<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('label')
            ->add('title')
            ->add('abstract')
            ->add('month')
            ->add('year')
            ->add('url_pdf')
            ->add('keywords')
            ->add('confEvents')
            ->add('conference')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Paper'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_papertype';
    }
}
