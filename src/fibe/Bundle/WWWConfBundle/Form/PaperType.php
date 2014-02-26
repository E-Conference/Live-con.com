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
            ->add('title')
            ->add('abstract', 'textarea', array( 'required' => true))
            ->add('publishDate')
            ->add('url')
            ->add('topics', 'entity', array(
                'class' => 'fibeWWWConfBundle:Topic',
                'label'   => 'Subjects',
                'choices'=> $this->user->getCurrentConf()->getTopics()->toArray(),
                'multiple'  => true,
                'required' => false
            ))
            ->add('authors', 'entity', array(
                'class' => 'fibeWWWConfBundle:Person',
                'label'   => 'Authors',
                'choices'=> $this->user->getCurrentConf()->getPersons()->toArray(),
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
