<?php

namespace fibe\SecurityBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\SecurityBundle\Entity\User;
use fibe\SecurityBundle\Entity\Authorization;
use fibe\SecurityBundle\Form\UserAuthorizationType;
use fibe\SecurityBundle\Form\AuthorizationType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/list", name="schedule_user_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }

          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $currentConf =$this->getUser()->getcurrentConf();
        $authorization = $user->getAuthorizationByConference($currentConf);

         if(!$authorization->getFlagTeam()){
            //throw new AccessDeniedException('Action not authorized !');
            return $this->redirect($this->generateUrl('schedule_conference_show')); 
          }

       
        $em = $this->getDoctrine()->getManager();

        $entities = $this->getUser()->getCurrentConf()->getConfManagers();
        $managers = $em->getRepository('fibeSecurityBundle:User')->findAll();
        $delete_forms= array();
        $update_forms= array();


        $authorizationForm = $this->createForm(new AuthorizationType($this->getUser(),false), new Authorization());

        foreach($entities as $entity ){
            $delete_forms[] = $this->createDeleteForm($entity->getId())->createView();
            $user = new User();

            $update_forms[] = $this->createFormBuilder($user)
                                    ->add('roles')
                                    ->getForm();
        }
        return array(
            'entities'     => $entities,
            'delete_forms' => $delete_forms,
            'update_forms' => $update_forms,
            'authorization_form' => $authorizationForm->createView(),
            'currentConf'  => $currentConf,
            'managers'     =>$managers
        );
    }

    
    /**
     * switch between admin and user role
     *
     * @Route("/toggle/{id}", name="schedule_user_toggle_role") 
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('fibeSecurityBundle:User')->find($id);
        if($user->hasRole('ROLE_ADMIN'))
        {
          $user->removeRole('ROLE_ADMIN');
          $em->persist($user);
          $em->flush();
          $this->container->get('session')->getFlashBag()->add(
              'success',
              'The user has been successfully demoted to manager.'
          );
        } else
        {
          $user->addRole('ROLE_ADMIN');
          $em->persist($user);
          $em->flush();
          $this->container->get('session')->getFlashBag()->add(
              'success',
              'The user has been successfully promoted to admin.'
          );
        }
        return $this->redirect($this->generateUrl('schedule_user_list')); 
    }



    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        throw new AccessDeniedHttpException('Unavailable on demo version');
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $entity = $em->getRepository('fibeSecurityBundle:User')->find($id);

          if (!$entity) {
              throw $this->createNotFoundException('Unable to find User entity.');
          }

          $em->remove($entity);
          $em->flush();
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'The user has been successfully removed.'
            );
        }else{
          $this->container->get('session')->getFlashBag()->add(
              'error',
              'Submition error, please try again.'
          ); 
        }

        return $this->redirect($this->generateUrl('schedule_user_list'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
