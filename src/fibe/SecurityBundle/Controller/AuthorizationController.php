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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * @Route("/authorization/update", name="schedule_user_change_authorization") 
     * TODO // Uniquement le createur de la conf peut ajouter une personne ou les modifier dans la conf
     */
    public function authorizationUpdateAction(Request $request)
    {
       /* if( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            // Sinon on déclenche une exception "Accès Interdit"
            throw new AccessDeniedHttpException('Access reserved to admin');
        }*/

        $id = $request->request->get('id');
        $authorizationType = $request->request->get('authorizationType');
        $value = $request->request->get('value');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('fibeSecurityBundle:User')->find($id);

        $currentConf = $this->getUser()->getCurrentConf();
        $authorization =  $em->getRepository('fibeSecurityBundle:Authorization')->findOneBy(array('conference' => $currentConf, 'user' => $user));

      
        switch ($authorizationType) {
          case 'app':
              $authorization->setFlagAppWR($value);
              break;
          case 'sched':
              $authorization->setFlagSchedWR($value);
              break;
          case 'datas':
             $authorization->setFlagconfDatasWR($value);
              break;
        }
       $em->persist($authorization);
       $em->flush();
       return new Response();
    }

     /**
     * Change authorization
     *
     * @Route("/authorization/create", name="schedule_user_create_authorization") 
     * TODO // Uniquement le createur de la conf peut ajouter une personne ou les modifier dans la conf
     */
      public function authorizationCreateAction(Request $request)
    {
        $entity  = new Authorization();

        $form = $this->createForm(new AuthorizationType($this->getUser()), $entity);

        $form->bind($request);

      // if ($form->isValid()) {
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
            
      //  }


        return $this->redirect($this->generateUrl('schedule_user_list'));
    }

    
   
}
