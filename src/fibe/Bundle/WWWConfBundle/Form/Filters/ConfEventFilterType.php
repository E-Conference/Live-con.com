<?php

namespace fibe\Bundle\WWWConfBundle\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class ConfEventFilterType extends AbstractType
{

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', 'entity', array(
                'class' => 'fibeWWWConfBundle:ConfEvent',
                'label'   => 'Name',
                'choices'=> $this->user->getCurrentConf()->getEvents()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Summary')
            ))
            ->add('location', 'entity', array(
                'class' => 'IDCISimpleScheduleBundle:Location',
                'label'   => 'Location',
                'choices'=> $this->user->getCurrentConf()->getLocations()->toArray(),
                'empty_data'  => null,
                'required' => false,
                'attr'  => array('placeholder'  => 'Location')
            ))
            ->add('category', 'entity', array(
                'class' => 'IDCISimpleScheduleBundle:Category',
                'label'   => 'Category',
                'query_builder'=> function(EntityRepository $er) {
                                return $er->extractQueryBuilder(array());
                             },
                'empty_data'  => null,
                'required' => false,
                'attr'  => array('placeholder'  => 'Category')
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
        return 'fibe_bundle_wwwconfbundle_confeventfiltertype';
    }
}
