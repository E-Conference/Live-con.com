<?php
  
namespace fibe\MobileAppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
use fibe\Bundle\WWWConfBundle\Form\WwwConfEventType;
 
 

class MobileAppWwwConfType extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', 'file',  array('required' => false, 
                                        'label'     => 'Logo',
                                        'attr'  => array('placeholder'   => 'logoPath')))
            ->add('mainConfEvent', new MobileAppWwwConfEventType($this->user),array(
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
