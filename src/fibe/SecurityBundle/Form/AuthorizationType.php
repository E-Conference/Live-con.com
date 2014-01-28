<?php

namespace fibe\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorizationType extends AbstractType
{
    
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {

           $builder
            ->add('user')
            ->add('flagApp','checkbox', array(
                              'required' => false,
                              'label' => 'Mobile Application Manager',
                              'attr'  => array('class' => 'switch switch-small',
                                          'data-on-label' => "<i class='fa fa-check fa-white'>",
                                          'data-off-label' => "<i class='fa fa-ban'>",
                               ))
            )
            ->add('flagSched','checkbox', array(
                               'required' => false,
                               'label' => 'Schedule Manager',
                               'attr'  => array('class' => 'switch switch-small',
                                          'data-on-label' => "<i class='fa fa-check fa-white'>",
                                          'data-off-label' => "<i class='fa fa-ban'>",
                               ))
                          
            )
            ->add('flagconfDatas','checkbox', array(
                              'required' => false,
                              'label' => 'Datas Conference Manager',
                               'attr' => array('class' => 'switch switch-small',
                                          'data-on-label' => "<i class='fa fa-check fa-white'>",
                                          'data-off-label' => "<i class='fa fa-ban'>",
                               ))
                               
            );

            if(count($this->user->getCurrentConf()->getConfManagers()) <= 1){
                $builder
                  ->add('flagTeam','checkbox', array(
                                  'required' => false,
                                  'label' => 'Team Manager',
                                  'disabled' => true,
                                  'attr'  => array('class' => 'switch switch-small',
                                              'data-on-label' => "<i class='fa fa-check fa-white'>",
                                              'data-off-label' => "<i class='fa fa-ban'>",
                                   ))
                );

            }else{

                $builder
                  ->add('flagTeam','checkbox', array(
                                  'required' => false,
                                  'label' => 'Team Manager',
                                  'attr'  => array('class' => 'switch switch-small',
                                              'data-on-label' => "<i class='fa fa-check fa-white'>",
                                              'data-off-label' => "<i class='fa fa-ban'>",
                                   ))
                );
            }
               
            
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'fibe\SecurityBundle\Entity\Authorization'
        ));
    }

    public function getName()
    {
        return 'fibe_securitybundle_authorizationtype';
    }
}
