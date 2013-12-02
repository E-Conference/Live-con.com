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
            ->add('flagAppWR','choice', array(
                              'choices' => array(false => 'Read', true => 'Read/Write'))
            )
            ->add('flagSchedWR','choice', array(
                              'choices' => array(false => 'Read', true => 'Read/Write'))
            )
            ->add('flagconfDatasWR','choice', array(
                              'choices' => array(false => 'Read', true => 'Read/Write'))
            )
            ->add('user')
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
