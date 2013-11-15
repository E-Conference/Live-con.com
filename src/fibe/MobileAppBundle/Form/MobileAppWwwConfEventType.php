<?php
  
namespace fibe\MobileAppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface; 
 
use fibe\Bundle\WWWConfBundle\Form\WwwConfEventType;

class MobileAppWwwConfEventType extends WwwConfEventType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
       $builder 
            ->remove('location')
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