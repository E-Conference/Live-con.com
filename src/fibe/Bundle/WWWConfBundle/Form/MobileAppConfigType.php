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
            ->add('BGColorContent', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Content background',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('BGColorHeader', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Header background',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('BGColorNavBar', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Nav barre background',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('BGColorfooter', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Footer background',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('ColorContentTitle', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Content titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('ColorHeaderTitle', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Header titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('ColorNavBarTitle', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Nav Barre titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
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
