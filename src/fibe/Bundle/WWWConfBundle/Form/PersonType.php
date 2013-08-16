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
            ->add('createdAt', 'datetime', array(
							   'widget' =>'single_text',
							   'format' =>'dd/MM/yyyy HH:mm', 
						))
            ->add('agent')
            ->add('name')
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
            ->add('group')
            ->add('member')
            ->add('document')
            ->add('image')
            ->add('confEvents')
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
