<?php

namespace fibe\Bundle\WWWConfBundle\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonFilterType extends AbstractType
{

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'entity', array(
                'class' => 'fibeWWWConfBundle:Person',
                'label'   => 'Name',
                'choices'=> $this->user->getCurrentConf()->getPersons()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Name')

            ))
             ->add('email', 'entity', array(
                'class' => 'fibeWWWConfBundle:Person',
                'label'   => 'Email',
                'property' => 'email',
                'choices'=> $this->user->getCurrentConf()->getPersons()->toArray(),
                'required' => false,
                 'attr'  => array('placeholder'  => 'Email')
            ));

        if($this->user->getCurrentConf()->getModule()->getOrganizationModule()==1){
            $builder
            ->add('organization', 'entity', array(
                'class' => 'fibeWWWConfBundle:Organization',
                'label'   => 'Organization',
                'choices'=> $this->user->getCurrentConf()->getOrganizations()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Organization')
            ));
        }
        if($this->user->getCurrentConf()->getModule()->getPaperModule()==1){
            $builder
            ->add('paper', 'entity', array(
                'class' => 'fibeWWWConfBundle:Paper',
                'label'   => 'Publication',
                'choices'=> $this->user->getCurrentConf()->getPapers()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Publication')
            ));

        }
        
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
         $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_personfiltertype';
    }
}
