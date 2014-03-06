<?php
  
namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 

class WwwConfType extends AbstractType
{
   private $user;
   private $entity;

    public function __construct($user,$entity)
    {
        $this->user   = $user;
        $this->entity = $entity;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', 'file',  array('required' => false, 
                                        'label'     => 'Logo (jpeg - png - 2MO)',
                                        'attr'  => array('placeholder'   => 'logoPath')))
            ->add('mainConfEvent', new WwwConfEventType($this->user,$this->entity),array(
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
