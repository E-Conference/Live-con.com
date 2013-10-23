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
        parent::buildForm($builder, $options);
       $builder
            // ->add('conference', null, array(
            //                         'required'  => true,
            //                         'label'     => 'Belongs to conf'
            // ))     
             ->add('startAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
              
            ))
            ->add('endAt', 'datetime', array(  
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy HH:mm', 
             
            ))    

            ->add('location', 'choice', array(
                'label'   => 'Location',
                'choices' => $this->user->getCurrentConf()->getLocations()->toArray()
            ))

            ->add('parent', 'choice', array(
                'label'   => 'Parent',
                'choices' => $this->user->getCurrentConf()->getEvents()->toArray()
            ))                     
            
 
        ;
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
