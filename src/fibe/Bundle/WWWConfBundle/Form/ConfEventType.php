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

    public function __construct($user)
    {
        $this->user = $user;
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
                 ->add('resources','text',array('required' => false, 'label'   => 'Twitter widget url')) 
                //  ->add('startAt', 'datetime', array(  
                //     'widget' =>'single_text',
                //     'format' =>'dd/MM/yyyy HH:mm', 
                  
                // ))
                // ->add('endAt', 'datetime', array(  
                //     'widget' =>'single_text',
                //     'format' =>'dd/MM/yyyy HH:mm', 
                 
                // ))     
                ->add('location', 'entity', array(
                    'class' => 'IDCISimpleScheduleBundle:Location',
                    'label'   => 'Location',
                    'choices'=> $this->user->getCurrentConf()->getLocations()->toArray(),
                    'empty_data'  => null,
                    'required' => false,
                ))  
                //  ->add('parent', 'entity', array(
                //     'class' => 'IDCISimpleScheduleBundle:Event',
                //     'label'   => 'Parent',
                //     'choices'=> $this->user->getCurrentConf()->getEvents()->toArray(),
                //     'empty_data'  => null,
                //     'required' => false,
                // ))
            ;        
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
