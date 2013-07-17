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

class XPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('xnamespace', 'choice', array(
                      'label'   =>'Link type',
                      'choices' => array('publication_uri' => 'publication', 
                                           'event_uri'       => 'event')))
            ->add('xkey', null, array('label' =>'Name'))
            ->add('xvalue', null, array('label' =>'Uri'))
            ->add('calendarEntity', null, array(
                'label' => ' ',
                'attr'=> array('style'=>'display:none')
            ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IDCI\Bundle\SimpleScheduleBundle\Entity\XProperty'
        ));
    }

    public function getName()
    {
        return 'idci_bundle_simpleschedulebundle_xpropertytype';
    }
}
