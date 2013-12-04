<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Form\PersonType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * Person controller.
 * @Route("/person")
 */
class PersonController extends Controller
{
    /**
     * Lists all Person entities.
     *
     * @Route("/", name="schedule_person_index")
     * @Template()
     */
     
    public function indexAction(Request $request)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        $currentConf = $this->getUser()->getCurrentConf();
        $entities = $currentConf->getPersons()->toArray();

        $adapter = new ArrayAdapter($entities);
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

        try {
            $pager->setCurrentPage($request->query->get('page', 1));
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return array(
            'pager' => $pager,
            'authorized' => $authorization->getFlagconfDatas(),
        );
    }

    /**
     * Creates a new Person entity.
     * @Route("/create", name="schedule_person_create")
     * @Template()
     */
    public function createAction(Request $request)
    {
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity  = new Person();
        $form = $this->createForm(new PersonType($this->getUser()), $entity);
        $form->bind($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());

            foreach($entity->getPapers() as $paper) { 
                $paper->addAuthor($entity);
                //$entity->addMember($person);
                $em->persist($paper);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_person_show', array('id' => $entity->getId())));

        }
        var_dump($form->getErrors());

        return $this->render('fibeWWWConfBundle:Person:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Displays a form to create a new Person entity.
     * @Route("/new", name="schedule_person_new")
     * @Template()
     */
    public function newAction()
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity = new Person();
        $form   = $this->createForm(new PersonType($this->getUser()), $entity);

        return $this->render('fibeWWWConfBundle:Person:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Finds and displays a Person entity.
     * @Route("/{id}/show", name="schedule_person_show")
     * @Template()
     */
    public function showAction($id)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Person:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),        ));
    }

    /**
     * Displays a form to edit an existing Person entity.
     * @Route("/{id}/edit", name="schedule_person_edit")
     * @Template()
     */
    public function editAction($id)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

           $em = $this->getDoctrine()->getManager();
        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Person')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $editForm = $this->createForm(new PersonType($this->getUser()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Person:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Edits an existing Person entity.
     * @Route("/{id}/update", name="schedule_person_update")
     */
    public function updateAction(Request $request, $id)
    {
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();

        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Person')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PersonType($this->getUser()), $entity);
        $papersToRemove = $entity->getPapers();
        
        foreach($papersToRemove as $paper) { 
            $paper->removeAuthor($entity);
            $entity->removePaper($paper);
            $em->persist($paper);
         }

        $editForm->bind($request);
        $paperToAdd = $entity->getPapers();
        if ($editForm->isValid()) {

            foreach($paperToAdd as $paper) { 
                $paper->addAuthor($entity);
                //$entity->addMember($person);
                $em->persist($paper);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_person_index'));
        }

        return $this->render('fibeWWWConfBundle:Person:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas(),
        ));
    }

    /**
     * Deletes a Person entity.
     * @Route("/{id}/delete", name="schedule_person_delete")
     * @Method({"POST", "DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //The object have to belongs to the current conf
            $currentConf=$this->getUser()->getCurrentConf();
            $entity =  $em->getRepository('fibeWWWConfBundle:Person')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Person entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_person_index'));
    }

    /**
     * Creates a form to delete a Person entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
