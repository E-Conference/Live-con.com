<?php

namespace fibe\ConferenceBundle\Controller;

use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\SecurityBundle\Entity\User;
use fibe\SecurityBundle\Entity\Authorization;
use fibe\SecurityBundle\Form\UserAuthorizationType;
use fibe\ConferenceBundle\Form\AuthorizationType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * User controller.
 *
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/list", name="conference_team_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {

          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $currentConf =$this->getUser()->getcurrentConf();
        $authorization = $user->getAuthorizationByConference($currentConf);

        if(!$this->getUser()->getAuthorizationByConference($this->getUser()->getcurrentConf())->getFlagTeam()==1 )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
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
            'managers'     =>$managers,
            'authorized' => $authorization->getFlagTeam(),
        );
    }

    
    // /**
    //  * switch between admin and user role
    //  *
    //  * @Route("/toggle/{id}", name="conference_team_toggle_role") 
    //  * @Template()
    //  */
    // public function updateAction(Request $request, $id)
    // {
    //     if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
    //     {
    //         // Sinon on déclenche une exception "Accès Interdit"
    //         throw new AccessDeniedHttpException('Access reserved to admin');
    //     }

    //     $em = $this->getDoctrine()->getManager();
    //     $user = $em->getRepository('fibeSecurityBundle:User')->find($id);
    //     if($user->hasRole('ROLE_ADMIN'))
    //     {
    //       $user->removeRole('ROLE_ADMIN');
    //       $em->persist($user);
    //       $em->flush();
    //       $this->container->get('session')->getFlashBag()->add(
    //           'success',
    //           'The user has been successfully demoted to manager.'
    //       );
    //     } else
    //     {
    //       $user->addRole('ROLE_ADMIN');
    //       $em->persist($user);
    //       $em->flush();
    //       $this->container->get('session')->getFlashBag()->add(
    //           'success',
    //           'The user has been successfully promoted to admin.'
    //       );
    //     }
    //     return $this->redirect($this->generateUrl('conference_team_list')); 
    // }



    /**
     * Deletes a teamate entity.
     *
     * @Route("/{id}", name="conference_team_delete")
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

        return $this->redirect($this->generateUrl('conference_team_list'));
    }

  /**
   * Creates a form to delete a User entity by id.
   *
   * @param mixed $id The entity id
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   * @return Form The form
   */
    private function createDeleteForm($id)
    { 
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}