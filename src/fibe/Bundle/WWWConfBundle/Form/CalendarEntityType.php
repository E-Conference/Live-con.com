<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use fibe\Bundle\WWWConfBundle\Entity\CalendarEntity;
use fibe\Bundle\WWWConfBundle\Repository\StatusRepository;
use fibe\Bundle\WWWConfBundle\Form\EventListener\RecurFieldSubscriber;

abstract class CalendarEntityType extends AbstractType
{

    private $user;

    public function __construct($user)
    {
        $this->user   = $user;
    }

    abstract public function getEntityDiscr();

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new RecurFieldSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);

        $discr = $this->getEntityDiscr();

        $builder
            ->add('summary')
            ->add('parent', null, array('required' => false))
            ->add('categories', null, array('required' => false))
            ->add('location', null, array('required' => false))
            ->add('startAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
                'attr' => array('class' => 'datetimepicker')
            ))
            /*
            ->add('options', 'choice', array(
                'choices' => array(
                    'all_day' => 'All the day',
                    'is_recur' => 'Recurrence'
                ),
                'multiple' => true,
                'expanded' => true
            ))
            ->add('includedRule', new RecurType(), array(
                'required' => false
            ))*/
            ->add('url')
            // ->add('parent')
            ->add('description')/*
            ->add('status', 'entity', array(
                'required'      => false,
                'class'         => 'fibeWWWConfBundle:Status',
                'query_builder' => function(StatusRepository $sr) use($discr) {
                    return $sr->getDiscrStatusQueryBuilder($discr);
                }
            ))
            ->add('classification', 'choice', array(
                'choices'  => CalendarEntity::getClassifications(),
                'multiple' => false,
                'expanded' => true
            ))*/
            ->add('comment')
            ->add('organizer')
            ->add('contacts')
            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\CalendarEntity'
        ));
    }

    public function getName()
    {
        return 'idci_simpleschedule_calendarentity_type';
    }
}
