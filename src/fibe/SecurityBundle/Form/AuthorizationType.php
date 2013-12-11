<?php

namespace fibe\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user')
            ->add('flagApp','choice', array(
                              'choices' => array(false => 'No', true => 'Yes'),
                              'label' => 'Mobile Application Manager'
                               )
            )
            ->add('flagSched','choice', array(
                              'choices' => array(false => 'No', true => 'Yes'),
                               'label' => 'Schedule Manager'
                               )
            )
            ->add('flagconfDatas','choice', array(
                              'choices' => array(false => 'No', true => 'Yes'),
                              'label' => 'Datas Conference Manager'
                               )
            )
            ->add('flagTeam','choice', array(
                              'choices' => array(false => 'No', true => 'Yes'),
                              'label' => 'Team Manager'
                               )
            )
           
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\SecurityBundle\Entity\Authorization'
        ));
    }

    public function getName()
    {
        return 'fibe_securitybundle_authorizationtype';
    }
}
