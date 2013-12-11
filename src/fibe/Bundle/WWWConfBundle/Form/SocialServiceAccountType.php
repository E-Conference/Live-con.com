<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SocialServiceAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accountName')
            ->add('socialService','entity', array(
                'class' => 'fibeWWWConfBundle:SocialService',
                'label'   => 'Social service',
                'required' => true ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_socialserviceaccounttype';
    }
}
