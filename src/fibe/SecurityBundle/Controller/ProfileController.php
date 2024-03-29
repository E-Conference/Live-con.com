<?php

namespace fibe\SecurityBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends ContainerAware
{ 
    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');



        $eventEdit = new GetResponseUserEvent($user, $request); 
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $eventEdit); 

        if (null !== $eventEdit->getResponse()){
            return $eventEdit->getResponse();
        }

        $formEdit = $this->container->get('fos_user.profile.form.factory')->createForm();
        $formPwd = $this->container->get('fos_user.change_password.form.factory')->createForm();
        $formEdit->setData($user);
        $formPwd->setData($user);




        if ('POST' === $request->getMethod()) {
            $formEdit->bind($request);

            if ($formEdit->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($formEdit, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_edit');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response)); 
                return $response;
            }else{

                $this->container->get('session')->getFlashBag()->add(
                    'error',
                    'Submition error, please try again.'
                );

            }
        }

        return $this->container->get('templating')->renderResponse(
            'fibeSecurityBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'formEdit' => $formEdit->createView(),
                'formPwd' => $formPwd->createView()
            )
        );
    }
}
