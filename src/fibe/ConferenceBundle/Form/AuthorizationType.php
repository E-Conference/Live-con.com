// <?php

//   namespace fibe\ConferenceBundle\Form;

//   use Symfony\Component\Form\AbstractType;
//   use Symfony\Component\Form\FormBuilderInterface;
//   use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//   use Doctrine\ORM\EntityRepository;

//   /**
//    * @TODO comment
//    *
//    * Class AuthorizationType
//    * @package fibe\SecurityBundle\Form
//    */
//   class AuthorizationType extends AbstractType
//   {

//     private $user;
//     private $disabled;

//     /**
//      * @param $user
//      * @param $disabled
//      */
//     public function __construct($user, $disabled)
//     {
//       $this->user = $user;
//       $this->disabled = $disabled;
//     }


//     /**
//      * {@inheritdoc}
//      */
//     public function buildForm(FormBuilderInterface $builder, array $options)
//     {

//       $builder
//         ->add('user', 'entity', array(
//           'class'         => 'fibeSecurityBundle:User',
//           'query_builder' => function (EntityRepository $er)
//           {
//             return $er->ManagerForSelectTeamQuery($this->user->getCurrentConf());

//           },
//         ))
//         ->add('flagApp', 'checkbox', array(
//             'required' => false,
//             'label'    => 'Mobile Application Manager',
//             'attr'     => array('class'          => 'switch switch-small',
//                                 'data-on-label'  => "<i class='fa fa-check fa-white'>",
//                                 'data-off-label' => "<i class='fa fa-ban'>",
//             ))
//         )
//         ->add('flagSched', 'checkbox', array(
//             'required' => false,
//             'label'    => 'Schedule Manager',
//             'attr'     => array('class'          => 'switch switch-small',
//                                 'data-on-label'  => "<i class='fa fa-check fa-white'>",
//                                 'data-off-label' => "<i class='fa fa-ban'>",
//             ))

//         )
//         ->add('flagconfDatas', 'checkbox', array(
//             'required' => false,
//             'label'    => 'Datas Conference Manager',
//             'attr'     => array('class'          => 'switch switch-small',
//                                 'data-on-label'  => "<i class='fa fa-check fa-white'>",
//                                 'data-off-label' => "<i class='fa fa-ban'>",
//             ))

//         );

//       if (count($this->user->getCurrentConf()->getConfManagers()) <= 1 && $this->disabled)
//       {
//         $builder
//           ->add('flagTeam', 'checkbox', array(
//               'required' => false,
//               'label'    => 'Team Manager',
//               'disabled' => true,
//               'attr'     => array('class'          => 'switch switch-small',
//                                   'data-on-label'  => "<i class='fa fa-check fa-white'>",
//                                   'data-off-label' => "<i class='fa fa-ban'>",
//               ))
//           );

//       }
//       else
//       {

//         $builder
//           ->add('flagTeam', 'checkbox', array(
//               'required' => false,
//               'label'    => 'Team Manager',
//               'attr'     => array('class'          => 'switch switch-small',
//                                   'data-on-label'  => "<i class='fa fa-check fa-white'>",
//                                   'data-off-label' => "<i class='fa fa-ban'>",
//               ))
//           );
//       }


//     }


//     /**
//      * {@inheritdoc}
//      */
//     public function setDefaultOptions(OptionsResolverInterface $resolver)
//     {
//       $resolver->setDefaults(array(
//         'data_class' => 'fibe\SecurityBundle\Entity\Authorization'
//       ));
//     }

//     /**
//      * Returns the name of this type.
//      *
//      * @return string The name of this type
//      */
//     public function getName()
//     {
//       return 'fibe_securitybundle_authorizationtype';
//     }
//   }
