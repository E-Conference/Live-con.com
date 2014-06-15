<?php

namespace fibe\SecurityBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Override edit and show action of FOS\UserBundle\Controller\ProfileController
 */
class ProfileController extends BaseController
{
 
  /**
   * Show the user
   * @Template()
   */
  public function showAction()
  {
      $user = $this->container->get('security.context')->getToken()->getUser();
      if (!is_object($user) || !$user instanceof UserInterface) {
          throw new AccessDeniedException('This user does not have access to this section.');
      }
      return array('user' => $user);
  }

  /**
   * Edit the user
   * @Template()
   */
  public function editAction()
  {
      $user = $this->container->get('security.context')->getToken()->getUser();
      if (!is_object($user) || !$user instanceof UserInterface) {
          throw new AccessDeniedException('This user does not have access to this section.');
      }

      $form = $this->container->get('fos_user.profile.form');
      $formPwd = $this->container->get('fos_user.change_password.form');

      $formHandler = $this->container->get('fos_user.profile.form.handler');

      $process = $formHandler->process($user);
      if ($process) { 
        $this->container->get('session')->getFlashBag()->add(
          'success',
          'profile updated'
        );
      }

      return array(
        'formEdit' => $form->createView(),
        'formPwd'  => $formPwd->createView()
      );
  }
}
