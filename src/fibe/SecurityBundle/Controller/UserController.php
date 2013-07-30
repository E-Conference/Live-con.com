<?php

namespace fibe\SecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\SecurityBundle\Entity\User;
use fibe\SecurityBundle\Form\UserType;
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
     * @Route("/list", name="wwwconf_user_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('fibeSecurityBundle:User')->findAll();
        $delete_forms= array();

        foreach($entities as $entity ){
            $delete_forms[] = $this->createDeleteForm($entity->getId())->createView();
        }
        return array(
            'entities'     => $entities,
            'delete_forms' => $delete_forms,
        );
    }  

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }
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

        return $this->redirect($this->generateUrl('wwwconf_user_list'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        throw new AccessDeniedHttpException('Unavailable on demo version');
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
