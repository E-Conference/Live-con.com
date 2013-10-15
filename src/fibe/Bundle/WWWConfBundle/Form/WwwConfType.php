<?php
  
namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 

class WwwConfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acronym', 'text', array('required' => false,
                                        'label'     => 'Acronym',
                                        'attr'  => array('placeholder'   => 'Acronym')))
             ->add('logo', 'text', array('required' => false,
                                        'label'     => 'Logo',
                                        'attr'  => array('placeholder'   => 'Logo uri')))
            ->add('mainConfEvent', new WwwConfEventType(),array(
                                        'label' => 'Conference event',
                                        'attr'  => array('class'   => 'well'))) 
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\WwwConf',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_wwwconftype';
    }
}
