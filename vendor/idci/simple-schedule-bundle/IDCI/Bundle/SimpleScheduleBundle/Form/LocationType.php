<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\SimpleScheduleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text')
            ->add('capacity')
            ->add('description')
            ->add('latitude')
            ->add('longitude')
            ->add('equipments', 'entity', array(
                'class' => 'fibeWWWConfBundle:Equipment',
                'label'   => 'Equipment',
                'required' => false,
                'multiple'  => true
            ))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IDCI\Bundle\SimpleScheduleBundle\Entity\Location'
        ));
    }

    public function getName()
    {
        return 'idci_bundle_simpleschedulebundle_locationtype';
    }
}
