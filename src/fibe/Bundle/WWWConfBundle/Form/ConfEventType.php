<?php
  
namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
 

class ConfEventType extends EventType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
       $builder
            // ->add('conference', null, array(
            //                         'required'  => true,
            //                         'label'     => 'Belongs to conf'
            // ))     
             ->add('startAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
              
            ))
            ->add('endAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
             
            ))                          
            
 
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
