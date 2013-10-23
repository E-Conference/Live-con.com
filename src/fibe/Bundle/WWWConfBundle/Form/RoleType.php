<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
{
     private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', 'entity', array(
                'class' => 'fibeWWWConfBundle:Person',
                'label'   => 'Person',
                'choices'=> $this->user->getCurrentConf()->getPersons()->toArray(),
                'required' => true
            ))
            ->add('type',null,array('required' => true))
      
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Role'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_roletype';
    }
}
