<?php

namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaperType extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => "Title *"))
            ->add('abstract', 'text', array('label' => "Abstract", 'required' => false))
            ->add('publishDate')
            ->add('url', 'text', array('label' => "Url *"))
            ->add('subjects', 'entity', array(
                'class' => 'fibeWWWConfBundle:Keyword',
                'label'   => 'Subjects',
                'choices'=> $this->user->getCurrentConf()->getKeywords()->toArray(),
                'multiple'  => true,
                'required' => false
            ))    
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\Bundle\WWWConfBundle\Entity\Paper'
        ));
    }

    public function getName()
    {
        return 'fibe_bundle_wwwconfbundle_papertype';
    }
}
