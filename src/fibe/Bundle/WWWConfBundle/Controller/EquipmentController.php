<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use fibe\Bundle\WWWConfBundle\Entity\Equipment;
use fibe\Bundle\WWWConfBundle\Form\EquipmentType;

/**
 * Equipment controller.
 * @Route("/schedule/equipment")
 */
class EquipmentController extends Controller
{
    /**
     * Lists all Equipment entities.
    *
    * @Route("/", name="equipment_index")
    * @Template()
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('fibeWWWConfBundle:Equipment')->findAll();

        return $this->render('fibeWWWConfBundle:Equipment:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Equipment entity.
     *  @Route("/create", name="equipment_create")
	* @Template()
     */
    public function createAction(Request $request)
    {
        $entity  = new Equipment();
        $form = $this->createForm(new EquipmentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('equipment_show', array('id' => $entity->getId())));
        }

        return $this->render('fibeWWWConfBundle:Equipment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Equipment entity.
     * @Route("/new", name="equipment_new")
	* @Template()
     */
    public function newAction()
    {
        $entity = new Equipment();
        $form   = $this->createForm(new EquipmentType(), $entity);

        return $this->render('fibeWWWConfBundle:Equipment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Equipment entity.
     * @Route("/{id}/show", name="equipment_show")
	* @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Equipment:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Equipment entity.
     * @Route("/{id}/edit", name="equipment_edit")
	* @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipment entity.');
        }

        $editForm = $this->createForm(new EquipmentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:Equipment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Equipment entity.
     * @Route("/{id}/update", name="equipment_update")
     * 
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Equipment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EquipmentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('equipment_edit', array('id' => $id)));
        }

        return $this->render('fibeWWWConfBundle:Equipment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Equipment entity.
     * @Route("/{id}/delete", name="equipment_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Equipment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('equipment'));
    }

    /**
     * Creates a form to delete a Equipment entity by id.
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
