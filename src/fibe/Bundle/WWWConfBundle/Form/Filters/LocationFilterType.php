<?php

namespace fibe\Bundle\WWWConfBundle\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationFilterType extends AbstractType
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
                'class' => 'fibeWWWConfBundle:Location',
                'label'   => 'Name',
                'choices'=> $this->user->getCurrentConf()->getLocations()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Label')
            ))
           ->add('equipment', 'entity', array(
                'class' => 'fibeWWWConfBundle:Equipment',
                'label'   => 'Equipment',
                'required' => false,
                'attr'  => array('placeholder'  => 'Equipment')

            ))
             ->add('cap_min', 'number',array(
                'label'   => 'Cap. min',
                'required' => false,
               'attr'  => array('placeholder'  => 'min capacity')
            ))
            ->add('cap_max', 'number',array(
                'label'   => 'Cap. max',
                'required' => false,
                'attr'  => array('placeholder'  => 'max capacity')
            ))
        ;
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
        return 'fibe_bundle_wwwconfbundle_locationfiltertype';
    }
}
