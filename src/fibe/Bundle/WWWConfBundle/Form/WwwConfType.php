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
            ->add('confName', 'text', array('required' => false,
                                        'label'     => 'Conference name',
                                        'attr'  => array('placeholder'   => 'Conference name')))

             ->add('logo', 'text', array('required' => false,
                                        'label'     => 'Logo',
                                        'attr'  => array('placeholder'   => 'Logo uri')))

            ->add('acronym', 'text', array('required' => false,
                                        'label'     => 'Acronym',
                                        'attr'  => array('placeholder'   => 'Acronym')))
            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\WwwConf'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_wwwconftype';
    }
}
