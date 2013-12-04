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
                              'choices' => array(false => 'False', true => 'True'),
                              'label' => 'Mobile Application Manager'
                               )
            )
            ->add('flagSched','choice', array(
                              'choices' => array(false => 'False', true => 'True'),
                               'label' => 'Schedule Manager'
                               )
            )
            ->add('flagconfDatas','choice', array(
                              'choices' => array(false => 'False', true => 'True'),
                              'label' => 'Datas Conference Manager'
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
