<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganizationType extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }


   public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
             ->add('page', 'text', array('required' => false, 'label' => 'Homepage'))
            ->add('country')
            ->add('members', 'entity', array(
                'class' => 'fibeWWWConfBundle:Person',
                'label'   => 'Members',
                'multiple' => true,
                'choices'=> $this->user->getCurrentConf()->getPersons()->toArray(),
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Organization'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_organizationtype';
    }
}
