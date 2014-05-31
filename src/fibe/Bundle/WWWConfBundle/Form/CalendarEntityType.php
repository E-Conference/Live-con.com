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
// use fibe\Bundle\WWWConfBundle\Form\EventListener\RecurFieldSubscriber;

class CalendarEntityType extends AbstractType
{
    protected $user;

    public function __construct($user)
    { 
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $subscriber = new RecurFieldSubscriber($builder->getFormFactory());
        // $builder->addEventSubscriber($subscriber);

        // $discr = $this->getEntityDiscr();

        $builder
            ->add('summary', 'text', array('required'  => true))
            ->add('categories', null, array('required' => false))
            ->add('url')
            ->add('description')
            ->add('comment')
            ->add('organizer')
            ->add('contacts')
            /*
            ->add('startAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
            ))
            
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
            ))
            ->add('parent')
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
        return 'fibe_bundle_wwwconfbundle_calendarentity_type';
    }
}
