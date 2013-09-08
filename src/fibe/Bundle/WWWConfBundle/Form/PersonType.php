<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('agent')
            ->add('name')
            ->add('homepage')
            ->add('hashtag')
            ->add('title')
            ->add('img')
            ->add('depiction')
            ->add('familyName')
            ->add('givenName')
            ->add('based_near')
            ->add('knows')
            ->add('age')
            ->add('made')
            ->add('primary_topic')
            ->add('project')
            ->add('organization')
            ->add('_group')
            ->add('member')
            ->add('document')
            ->add('image')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Person'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_persontype';
    }
}
