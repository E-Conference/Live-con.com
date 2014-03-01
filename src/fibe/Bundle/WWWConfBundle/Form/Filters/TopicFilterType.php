<?php

namespace fibe\Bundle\WWWConfBundle\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopicFilterType extends AbstractType
{

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'entity', array(
                'class' => 'fibeWWWConfBundle:Topic',
                'label'   => 'Name',
                'choices'=> $this->user->getCurrentConf()->getTopics()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Label')
            ));
        if($this->user->getCurrentConf()->getModule()->getPaperModule()==1){
            $builder
            ->add('paper', 'entity', array(
                'class' => 'fibeWWWConfBundle:Paper',
                'label'   => 'Paper',
                'choices'=> $this->user->getCurrentConf()->getPapers()->toArray(),
                'required' => false,
                'attr'  => array('placeholder'  => 'Publication')
            ));
        }
             
    
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
         $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_topicfiltertype';
    }
}
