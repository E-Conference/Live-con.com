<?php
  
namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface; 
 

class WwwConfEventType extends ConfEventType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
       $builder 
            ->remove('startAt')
            ->remove('endAt')                          
            ->remove('categories')                          
            ->remove('location')                          
            ->remove('parent')  
            ->add('location', new LocationLatLngType(),array(
                                        'label' => 'Conference location (click on the map)',
                                        'attr'  => array('class'   => 'well')))                         
            
 
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\ConfEvent'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_confeventtype';
    }
}
