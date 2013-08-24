<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MobileAppConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('BGColorContent')
            ->add('BGColorHeader')
            ->add('BGColorNavBar')
            ->add('BGColorfooter')
            ->add('ColorContentTitle')
            ->add('ColorHeaderTitle')
            ->add('ColorNavBarTitle')
            ->add('IsPublished')
            ->add('Conference')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\MobileAppConfig'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_mobileappconfigtype';
    }
}
