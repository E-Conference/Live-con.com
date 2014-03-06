<?php
  
namespace fibe\Bundle\WWWConfBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IDCI\Bundle\SimpleScheduleBundle\Form\EventType;
use IDCI\Bundle\SimpleScheduleBundle\Entity\Location;
 

class ConfEventType extends EventType
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
       
      if($this->user->getCurrentConf()){

           parent::buildForm($builder, $options);
           $builder
                // ->add('conference', null, array(
                //                         'required'  => true,
                //                         'label'     => 'Belongs to conf'
                // ))   
                ->add('summary','text',array('required' => true)) 
                ->add('acronym', 'text', array('required' => false,
                                            'label'     => 'Acronym',
                                            'attr'  => array('placeholder'   => 'Acronym')))
                 ->add('categories',null,array('required' => false)) 
                 ->add('attach','text',array('required' => false, 'label'   => 'Twitter widget id')) 
                 ->remove('startAt' )
                 ->remove('endAt')
                 ->remove('parent')
                //  ->add('parent', 'entity', array(
                //     'class' => 'IDCISimpleScheduleBundle:Event',
                //     'label'   => 'Parent',
                //     'choices'=> $this->user->getCurrentConf()->getEvents()->toArray(),
                //     'empty_data'  => null,
                //     'required' => false,
                // ))
            ;    

            if($this->entity->hasChildren()){
                // $builder->add('location', 'entity', array(
                //     'class' => 'IDCISimpleScheduleBundle:Location',
                //     'label'   => 'Location',
                //     'choices'=> $this->user->getCurrentConf()->getLocations()->toArray(),
                //     'empty_data'  => null,
                //     'required' => false,
                //     // not working probably due to a twig behavior
                //     // 'attr' => array('onload' => "$(this).parent().remove();")
                // ));  
                $builder->remove('location');
            }
        }else{

             parent::buildForm($builder, $options);
             $builder
                // ->add('conference', null, array(
                //                         'required'  => true,
                //                         'label'     => 'Belongs to conf'
                // ))   
                ->add('summary','text',array('required' => true)) 
                ->add('acronym', 'text', array('required' => false,
                                            'label'     => 'Acronym',
                                            'attr'  => array('placeholder'   => 'Acronym')))
                ->add('categories',null,array('required' => false)) 
                 ;

        }
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
