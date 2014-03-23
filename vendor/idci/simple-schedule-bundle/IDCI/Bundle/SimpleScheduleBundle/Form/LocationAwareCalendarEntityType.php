<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\SimpleScheduleBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class LocationAwareCalendarEntityType extends CalendarEntityType
{
   
    private $user;

    public function __construct($user)
    {
        parent::__construct($user);
        $this->user   = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('location', 'entity', array(
                'class' => 'IDCISimpleScheduleBundle:Location',
                'label'   => 'Location',
                'choices'=> $this->user->getCurrentConf()->getLocations()->toArray(),
                'multiple'  => false,
                'required' => false
            )) 
            // ->add('priority', 'choice', array(
            //     'choices' => range(0, 9)
            // ))
            // ->add('resources')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IDCI\Bundle\SimpleScheduleBundle\Entity\LocationAwareCalendarEntity'
        ));
    }

    public function getName()
    {
        return 'idci_simpleschedule_event_type';
    }
}
