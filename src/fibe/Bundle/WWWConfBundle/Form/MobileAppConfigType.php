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
           
            ->add('BGColorHeader', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Header background',
                                    'attr'  => array('class'   => 'color form-control')
            ))
            ->add('TitleColorHeader', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Header titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('BGColorNavBar', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Nav barre background',
                                    'attr'  => array('class'   => 'color form-control')
            ))
            ->add('TitleColorNavBar', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Nav Barre titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))
             ->add('BGColorContent', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Content background',
                                    'attr'  => array('class'   => 'color form-control')
            )) 
            ->add('TitleColorContent', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Content titles',
                                    'attr'  => array('class'   => 'color form-control')
            ))
            ->add('BGColorButton', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Button background',
                                    'attr'  => array('class'   => 'color form-control')
            )) 
            ->add('TitleColorButton', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Button text',
                                    'attr'  => array('class'   => 'color form-control')
            ))     
            ->add('BGColorFooter', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Footer background',
                                    'attr'  => array('class'   => 'color form-control')
            ))
            ->add('TitleColorFooter', 'text', array(
                                    'required'  => true,
                                    'label'     => 'Footer titles',
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
