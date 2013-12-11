<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Organization;
use fibe\Bundle\WWWConfBundle\Form\OrganizationType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 

/**
 * Organization controller.
 *
 * @Route("/organization")
 */
class OrganizationController extends Controller
{
    /**
     * Lists all Organization entities.
     *
     * @Route("/", name="schedule_organization_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        
        //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        $conf = $this->getUser()->getCurrentConf();
        $entities = $conf->getOrganizations()->toArray();

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
            'authorized' => $authorization->getFlagconfDatas() 
        );
    }

    /**
     * Creates a new Organization entity.
     *
     * @Route("/create", name="schedule_organization_create")
     * @Method("POST")
     * @Template("fibeWWWConfBundle:Organization:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity  = new Organization();
        $form = $this->createForm(new OrganizationType($this->getUser()), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());

            foreach($entity->getMembers() as $person) { 
                $person->addOrganization($entity);
                //$entity->addMember($person);
                $em->persist($person);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_organization_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas()
        );
    }

    /**
     * Displays a form to create a new Organization entity.
     *
     * @Route("/new", name="schedule_organization_new")
     * @Method("GET")
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

        $entity = new Organization();
        $form   = $this->createForm(new OrganizationType($this->getUser()), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagconfDatas()
        );
    }

    /**
     * Finds and displays a Organization entity.
     *
     * @Route("/{id}/show", name="schedule_organization_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:Organization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas()
        );
    }

    /**
     * Displays a form to edit an existing Organization entity.
     *
     * @Route("/{id}/edit", name="schedule_organization_edit")
     * @Method("GET")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:Organization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $editForm = $this->createForm(new OrganizationType($this->getUser()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas()
        );
    }

    /**
     * Edits an existing Organization entity.
     *
     * @Route("/{id}/update", name="schedule_organization_update")
     * @Method("PUT")
     * @Template("fibeWWWConfBundle:Organization:edit.html.twig")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:Organization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organization entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OrganizationType($this->getUser()), $entity);
        $personToRemove = $entity->getMembers();
       
        
        foreach($personToRemove as $person) { 
            $person->removeOrganization($entity);
            $entity->removeMember($person);
            $em->persist($person);
        }
        
        $editForm->bind($request);
        $personToAdd = $entity->getMembers();
        if ($editForm->isValid()) {
    
            //Add members selected in forms to the current organization thank to the woning sir
            foreach($personToAdd as $person) { 
                $person->addOrganization($entity);
                //$entity->addMember($person);
                $em->persist($person);
            }

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('schedule_organization_index'));

            //return $this->redirect($this->generateUrl('schedule_organization_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorization->getFlagconfDatas()
        );
    }

    /**
     * Deletes a Organization entity.
     *
     * @Route("/{id}/delete", name="schedule_organization_delete")
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
            $entity =  $em->getRepository('fibeWWWConfBundle:Organization')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Organization entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_organization_index'));
    }

    /**
     * Creates a form to delete a Organization entity by id.
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
