<?php


  namespace fibe\SecurityBundle\Controller;


  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

  use FOS\UserBundle\FOSUserEvents;
  use FOS\UserBundle\Event\FormEvent;
  use FOS\UserBundle\Event\GetResponseUserEvent;
  use FOS\UserBundle\Event\UserEvent;
  use FOS\UserBundle\Event\FilterUserResponseEvent;
  use Symfony\Component\DependencyInjection\ContainerAware;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\RedirectResponse;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;
  use FOS\UserBundle\Model\UserInterface;
  use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
  use FOS\UserBundle\Controller\RegistrationController as BaseController;


  /**
   * Controller managing the registration
   *
   * @author Thibault Duplessis <thibault.duplessis@gmail.com>
   * @author Christophe Coevoet <stof@notk.org>
   * @Route("/register")
   */
  class NOTUSEDRegistrationController extends BaseController
  { 

    // public function registerAction(Request $request)
    // {
    //   /* if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
    //    {
    //        // Sinon on déclenche une exception "Accès Interdit"
    //        throw new AccessDeniedHttpException('Access reserved to admin');
    //    }*/
    //   /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
    //   $formFactory = $this->container->get('fos_user.registration.form.factory');
    //   /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
    //   $userManager = $this->container->get('fos_user.user_manager');
    //   /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    //   $dispatcher = $this->container->get('event_dispatcher');

    //   $user = $userManager->createUser();
    //   $user->setEnabled(true);


    //   $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

    //   $form = $formFactory->createForm();
    //   $form->setData($user);
    //   if ('POST' === $request->getMethod())
    //   {
    //     $form->bind($request);
    //     if ($form->isValid())
    //     {
    //       $event = new FormEvent($form, $request);
    //       $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
    //       // if($_POST['fos_user_registration_form']["is_admin"] === "on"){
    //       //     $user->addRole('ROLE_ADMIN');
    //       // }


    //       $user->addRole('ROLE_ADMIN');


    //       $userManager->updateUser($user);
    //       if (null === $response = $event->getResponse())
    //       {
    //         $url = $this->container->get('router')->generate('fos_user_registration_register');
    //         $response = new RedirectResponse($url);
    //       }
    //       //comment to avoid auto registering ; It's also used to auto flash message....
    //       //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

    //       $this->container->get('session')->getFlashBag()->add(
    //         'success',
    //         'The user has been successfully registered.'
    //       );

    //       return $response;
    //     }
    //     else
    //     {

    //       $this->container->get('session')->getFlashBag()->add(
    //         'error',
    //         'Submition error, please try again.'
    //       );

    //     }
    //   }

    //   return $this->container->get('templating')->renderResponse('fibeSecurityBundle:Registration:register.html.' . $this->getEngine(), array(
    //     'form' => $form->createView(),
    //   ));
    // }
  }
