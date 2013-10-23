<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;
use fibe\Bundle\WWWConfBundle\Form\RoleTypeType;

/**
 * RoleType controller.
 *
 * @Route("/schedule/roletype")
 */
class RoleTypeController extends Controller
{
    /**
     * Lists all RoleType entities.
     *
     * @Route("/", name="schedule_roletype")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('fibeWWWConfBundle:RoleType')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new RoleType entity.
     *
     * @Route("/", name="schedule_roletype_create")
     * @Method("POST")
     * @Template("fibeWWWConfBundle:RoleType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new RoleType();
        $form = $this->createForm(new RoleTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_roletype'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new RoleType entity.
     *
     * @Route("/new", name="schedule_roletype_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RoleType();
        $form   = $this->createForm(new RoleTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a RoleType entity.
     *
     * @Route("/{id}", name="schedule_roletype_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoleType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing RoleType entity.
     *
     * @Route("/{id}/edit", name="schedule_roletype_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoleType entity.');
        }

        $editForm = $this->createForm(new RoleTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing RoleType entity.
     *
     * @Route("/{id}", name="schedule_roletype_update")
     * @Method("PUT")
     * @Template("fibeWWWConfBundle:RoleType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RoleType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RoleTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_roletype'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a RoleType entity.
     *
     * @Route("/{id}", name="schedule_roletype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RoleType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_roletype'));
    }

    /**
     * Creates a form to delete a RoleType entity by id.
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
