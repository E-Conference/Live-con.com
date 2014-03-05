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
            ->add('firstName', 'text', array('label' => "First name"))
            ->add('familyName', 'text', array('label' => "Family Name"))
            ->add('email','text', array('required' => false))
            ->add('age', 'text', array('required' => false))
            ->add('page', 'text', array('required' => false, 'label' => 'Homepage'))
            ->add('img', 'text', array('required' => false, 'label' => 'Image'))
            ->add('openId', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false, 'label' => 'Description'))
            // ->add('nick', 'text', array('required' => false))
            ->add('organizations', 'entity', array(
                'class' => 'fibeWWWConfBundle:Organization',
                'label'   => 'Organizations',
                'choices'=> $this->user->getCurrentConf()->getOrganizations()->toArray(),
                'required' => false,
                'multiple'  => true
            ))
            ->add('papers', 'entity', array(
                'class' => 'fibeWWWConfBundle:Paper',
                'label'   => 'Publications',
                'choices'=> $this->user->getCurrentConf()->getPapers()->toArray(),
                'required' => false,
                'multiple'  => true
            ))

            ->add('accounts', 'collection',array('type' => new SocialServiceAccountType(),
                                                  'allow_add'  => true,
                                                  'allow_delete' => true))
           
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
