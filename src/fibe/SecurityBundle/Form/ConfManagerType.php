<?php
  
namespace fibe\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use fibe\Bundle\WWWConfBundle\Form\WwwConfType;

class ConfManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('wwwConf', 'collection', array( 'type'         => new WwwConfType(),
                                                  'allow_add'    => true,
                                                  'allow_delete' => true))
    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\SecurityBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_securitybundle_confmanagertype';
    }
}
