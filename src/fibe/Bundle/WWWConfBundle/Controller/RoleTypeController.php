<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;
use fibe\Bundle\WWWConfBundle\Form\RoleTypeType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'RoleType');

    return array(
      'entities' => $entities
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
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'RoleType');

    $form = $this->createForm(new RoleTypeType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('schedule_roletype'));
    }

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
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
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'RoleType');
    $form = $this->createForm(new RoleTypeType(), $entity);

    return array(
      'entity' => $entity,
      'form'   => $form->createView()
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
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'RoleType', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find RoleType entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
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
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'RoleType', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find RoleType entity.');
    }

    $editForm = $this->createForm(new RoleTypeType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
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
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'RoleType', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find RoleType entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new RoleTypeType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('schedule_roletype'));
    }

    return array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    );
  }

  /**
   * Deletes a RoleType entity.
   *
   * @Route("/{id}", name="schedule_roletype_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'RoleType', $id);

    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();

      if (!$entity)
      {
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
   * @return Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(array('id' => $id))
      ->add('id', 'hidden')
      ->getForm();
  }
}
