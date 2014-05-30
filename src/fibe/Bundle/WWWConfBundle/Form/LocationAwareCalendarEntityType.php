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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

abstract class LocationAwareCalendarEntityType extends CalendarEntityType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            //hide location field if the event has a child
            if (!$event->getForm()->has("location") && !$event->getData()->hasChildren()) {
                $event->getForm()->add('location', 'entity', array(
                    'class'    => 'fibeWWWConfBundle:Location',
                    'label'    => 'Location',
                    'choices'  => $this->user->getCurrentConf()->getLocations()->toArray(),
                    'multiple' => false,
                    'required' => false
                ));
            }
        });
            // ->add('priority', 'choice', array(
            //     'choices' => range(0, 9)
            // ))
            // ->add('resources')
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\LocationAwareCalendarEntity'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_event_type';
    }
}
