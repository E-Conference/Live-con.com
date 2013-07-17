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
            ->add('confName', null, array('required' => false,
                                          'attr'  => array(
                                                    'placeholder'   => 'Conference name')))
            ->add('confOwlUri', null, array('required' => false,
                                            'attr'     => array(
                                                    'placeholder'   => 'complete rdf file url')))
            ->add('confUri', null, array('attr'     => array(
                                                    'placeholder'   => 'Sparql endpoint url')))
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
