<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use fibe\Bundle\WWWConfBundle\Entity\CalendarEntity;

class EventType extends LocationAwareCalendarEntityType
{ 
   

    // public function buildForm(FormBuilderInterface $builder, array $options)
    // {
    //     parent::buildForm($builder, $options);
    //    /*
    //     $builder

    //         ->add('endAt', 'datetime', array(  
    //             'widget' =>'single_text',
    //             'format' =>'dd/MM/yyyy HH:mm', 
    //         ))
			
    //         ->add('isTransparent', null, array( 
			 //    'label' => 'is Transparent',
    //             'required' => false
    //         ))
    //     ;*/
        
    // }

    public function getEntityDiscr()
    {
        return CalendarEntity::EVENT;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Event'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_eventtype';
    }
}
