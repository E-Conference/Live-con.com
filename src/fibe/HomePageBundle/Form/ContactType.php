<?php

namespace fibe\HomePageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @TODO    comment
 *
 * Class ContactType
 * @package fibe\HomePageBundle\Form
 */
class ContactType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('nom', 'text', array(
        'label' => 'Last Name',
        'attr'  => array(
          'placeholder' => 'What\'s your first name?',
          'name'        => 'prenom',
          'class'       => 'prenom',
          'required'    => true,
        )
      ))
      ->add('prenom', 'text', array(
        'label' => 'First Name',
        'attr'  => array(
          'placeholder' => 'What\'s your last name?',
          'name'        => 'nom',
          'class'       => 'nom',
          'required'    => true,
        )
      ))
      ->add('email', 'email', array(
        'label' => 'Email',
        'attr'  => array(
          'placeholder' => 'So we can get back to you.',
          'name'        => 'email',
          'class'       => 'email',
          'required'    => true,
        )
      ))
      ->add('confname', 'text', array(
        'label' => 'Conference name',
        'attr'  => array(
          'placeholder' => 'conference name',
          'name'        => 'confname',
          'class'       => 'confname',
          'required'    => true,
        )
      ))
      ->add('message', 'text', array(
        'label' => 'Message',
        'attr'  => array(
          'rows'        => 8,
          'placeholder' => 'Your message to us...',
          'name'        => 'message',
          'class'       => 'message',
          'required'    => true,
        )
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $collectionConstraint = new Collection(array(
      'nom'      => array(
        new NotBlank(array('message' => 'Name should not be blank.')),
        new Length(array('min' => 2))
      ),
      'prenom'   => array(
        new NotBlank(array('message' => 'Name should not be blank.')),
        new Length(array('min' => 2))
      ),
      'email'    => array(
        new NotBlank(array('message' => 'Email should not be blank.')),
        new Email(array('message' => 'Invalid email address.'))
      ),
      'confname' => array(
        new NotBlank(array('message' => 'Conference Name should not be blank.')),
        new Length(array('min' => 2))
      ),
      'message'  => array(
        new NotBlank(array('message' => 'Message should not be blank.')),
        new Length(array('min' => 5))
      )
    ));

    $resolver->setDefaults(array(
      'constraints' => $collectionConstraint
    ));
  }

  /**
   * Returns the name of this type.
   *
   * @return string The name of this type
   */
  public function getName()
  {
    return 'contact';
  }
}