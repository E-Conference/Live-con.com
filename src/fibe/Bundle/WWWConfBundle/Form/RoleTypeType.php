<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('label')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\RoleType'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_roletypetype';
    }
}
