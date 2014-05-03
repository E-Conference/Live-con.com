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

use fibe\Bundle\WWWConfBundle\Repository\CalendarEntityRepository;

class CalendarEntityRelationType extends AbstractType
{
    protected $entity;

    function __construct(CalendarEntity $entity = null)
    {
        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $this->entity;
        $builder
            ->add('calendarEntity', null, array(
                'label' => ' ',
                'attr'=> array('style'=>'display:none')
            ))
            ->add('relatedTo', 'entity', array(
                'class'         => 'fibeWWWConfBundle:CalendarEntity',
                'empty_value'   => ' ',
                'query_builder' => function(CalendarEntityRepository $cer) use($entity) {
                    return $cer->getRelatedAvailableCalendarEntitiesQueryBuilder($entity);
                }
            ))
            ->add('relationType', 'choice', array(
                'choices' => CalendarEntityRelation::getRelationTypes()
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\CalendarEntityRelation'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_calendarentityrelation_type';
    }
}
