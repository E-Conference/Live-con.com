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

use IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntity;

class EventType extends LocationAwareCalendarEntityType
{
   
    private $user;

    public function __construct($user)
    {
        parent::__construct($user);
        $this->user   = $user;
    }

   public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

             ->add('startAt', 'datetime', array(
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm',

            ))
            ->add('endAt', 'datetime', array(
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm',

            ))
            // ->add('isTransparent', null, array(
            //     'label' => 'is Transparent',
            //     'required' => false
            // ))
        ;
        parent::buildForm($builder, $options);
    }

    public function getEntityDiscr()
    {
        return CalendarEntity::EVENT;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IDCI\Bundle\SimpleScheduleBundle\Entity\Event'
        ));
    }

    public function getName()
    {
        return 'idci_simpleschedule_event_type';
    }
}
