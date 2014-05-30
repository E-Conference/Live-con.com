<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\Module;
use fibe\Bundle\WWWConfBundle\Form\ModuleType;

/**
 * Module controller.
 *
 */
class ModuleController extends Controller
{

  /**
   * Lists all Module entities.
   *
   * @return Response
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('fibeWWWConfBundle:Module')->findAll();

    return $this->render(
      'fibeWWWConfBundle:Module:index.html.twig',
      array(
        'entities' => $entities,
      )
    );
  }


  /**
   * Creates a new Module entity.
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
   */
  public function createAction(Request $request)
  {
    $entity = new Module();
    $form = $this->createForm(new ModuleType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('module_show', array('id' => $entity->getId())));
    }

    return $this->render(
      'fibeWWWConfBundle:Module:new.html.twig',
      array(
        'entity' => $entity,
        'form' => $form->createView(),
      )
    );
  }


  /**
   * Displays a form to create a new Module entity.
   *
   * @return Response
   */
  public function newAction()
  {
    $entity = new Module();
    $form = $this->createForm(new ModuleType(), $entity);

    return $this->render(
      'fibeWWWConfBundle:Module:new.html.twig',
      array(
        'entity' => $entity,
        'form' => $form->createView(),
      )
    );
  }


  /**
   * Finds and displays a Module entity.
   *
   * @param $id
   *
   * @return Response
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Module entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return $this->render(
      'fibeWWWConfBundle:Module:show.html.twig',
      array(
        'entity' => $entity,
        'delete_form' => $deleteForm->createView(),
      )
    );
  }


  /**
   * Displays a form to edit an existing Module entity.
   *
   * @param $id
   *
   * @return Response
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Module entity.');
    }

    $editForm = $this->createForm(new ModuleType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return $this->render(
      'fibeWWWConfBundle:Module:edit.html.twig',
      array(
        'entity' => $entity,
        'edit_form' => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
      )
    );
  }


  /**
   * Edits an existing Module entity.
   * @Route("{id}/module", name="schedule_module_update")
   *
   * @param Request $request
   * @param         $id
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function updateAction(Request $request, $id)
  {

    //Authorization Verification conference datas manager
    $user = $this->getUser();
    $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

    if (!$authorization->getFlagconfDatas())
    {
      //throw new AccessDeniedException('Action not authorized !');
      return $this->redirect($this->generateUrl('schedule_conference_show'));
    }

    $em = $this->getDoctrine()->getManager();
    $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Module entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new ModuleType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em->persist($entity);
      $em->flush();

      $this->container->get('session')->getFlashBag()->add(
        'success',
        'The module is succesfully updated'
      );
    }
    else
    {

      $this->container->get('session')->getFlashBag()->add(
        'error',
        'The module cannot be saved'
      );
    }

    return $this->redirect($this->generateUrl('schedule_conference_settings'));
  }


  /**
   * Deletes a Module entity.
   *
   * @param Request $request
   * @param         $id
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Module entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('module'));
  }

  /**
   * Creates a form to delete a Module entity by id.
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
