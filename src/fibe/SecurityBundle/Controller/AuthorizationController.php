<?php

namespace fibe\SecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\SecurityBundle\Entity\User;
use fibe\SecurityBundle\Entity\Authorization;
use fibe\SecurityBundle\Form\UserType;
use fibe\SecurityBundle\Form\AuthorizationType;
use fibe\SecurityBundle\Form\UserAuthorizationType;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * User controller.
 *
 * @Route("/authorization")
 */
class AuthorizationController extends Controller
{
   
     /**
     * Change authorization
     *
     * @Route("/authorization/create", name="schedule_user_create_authorization") 
     * 
     */
      public function authorizationCreateAction(Request $request)
    {
        
        $currentConf = $this->getUser()->getCurrentConf();
        if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') && $this->getUser()->getAuthorizationByConference($currentConf)->getFlagTeam()==1 )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin and team manager');
        }
        $entity  = new Authorization();

        $form = $this->createForm(new AuthorizationType($this->getUser()), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            //Link the new Event to the current Conf 
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity); 
            $em->flush();

            //On ajoute la conf au user ajoute
            $user= $entity->getUser();
            $user->addConference($this->getUser()->getCurrentConf());
            $em->persist($user); 
            $em->flush();
            
       }


        return $this->redirect($this->generateUrl('schedule_user_list'));
    }


      /**
     * Displays a form to edit an existing Paper entity.
     * @Route("/{id}/edit", name="schedule_authorization_edit")
     * @Template()
     */
    public function editAction($id)
    {
        
           //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagTeam()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();

         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeSecurityBundle:Authorization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find authorization entity.');
        }

        $editForm = $this->createForm(new UserAuthorizationType($this->getUser()), $entity);
      

        return $this->render('fibeSecurityBundle:Authorization:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'authorized' => $authorization->getFlagTeam(),
        ));
    }

    /**
     * Edits an existing Paper entity.
     * @Route("/{id}/update", name="schedule_authorization_update")
     */
    public function updateAction(Request $request, $id)
    {
       
           //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagTeam()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();

         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeSecurityBundle:Authorization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find authorization entity.');
        }

        $editForm = $this->createForm(new UserAuthorizationType($this->getUser()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conference_team_list'));
        }

        return $this->redirect($this->generateUrl('schedule_authorization_edit', array('id' => $id)));
    }

    
   
}
