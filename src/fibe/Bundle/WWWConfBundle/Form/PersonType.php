<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonType extends AbstractType
{

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => "First name *"))
            ->add('familyName', 'text', array('label' => "Family Name *"))
            ->add('email','text', array('required' => false))
            ->add('age', 'text', array('required' => false))
            ->add('page', 'text', array('required' => false))
            ->add('img', 'text', array('required' => false))
            ->add('openId', 'text', array('required' => false))
            // ->add('nick', 'text', array('required' => false))
            ->add('organizations', 'entity', array(
                'class' => 'fibeWWWConfBundle:Organization',
                'label'   => 'Organizations',
                'choices'=> $this->user->getCurrentConf()->getOrganizations()->toArray(),
                'required' => false,
                'multiple'  => true
            ))
           
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
