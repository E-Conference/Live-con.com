<?php

  namespace fibe\Bundle\WWWConfBundle\Controller;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\Form\Form;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

  use fibe\Bundle\WWWConfBundle\Entity\Equipment;
  use fibe\Bundle\WWWConfBundle\Form\EquipmentType;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;

  /**
   * Equipment controller.
   * @Route("/equipment")
   */
  class EquipmentController extends Controller
  {
    /**
     * Lists all Equipment entities.
     *
     * @Route("/", name="schedule_equipment_index")
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
     *
     * @Route("/create", name="schedule_equipment_create")
     * @Template()
     */
    public function createAction(Request $request)
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      if (!$authorization->getFlagconfDatas())
      {
        throw new AccessDeniedException('Action not authorized !');
      }

      $entity = new Equipment();
      $form = $this->createForm(new EquipmentType(), $entity);
      $form->bind($request);

      if ($form->isValid())
      {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('schedule_equipment_show', array('id' => $entity->getId())));
      }

      return $this->render('fibeWWWConfBundle:Equipment:new.html.twig', array(
        'entity'     => $entity,
        'form'       => $form->createView(),
        'authorized' => $authorization->getFlagSched()
      ));
    }

    /**
     * Displays a form to create a new Equipment entity.
     *
     * @Route("/new", name="schedule_equipment_new")
     * @Template()
     */
    public function newAction()
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      if (!$authorization->getFlagconfDatas())
      {
        throw new AccessDeniedException('Action not authorized !');
      }

      $entity = new Equipment();
      $form = $this->createForm(new EquipmentType(), $entity);

      return $this->render('fibeWWWConfBundle:Equipment:new.html.twig', array(
        'entity'     => $entity,
        'form'       => $form->createView(),
        'authorized' => $authorization->getFlagSched()
      ));
    }

    /**
     * Finds and displays a Equipment entity.
     *
     * @Route("/{id}/show", name="schedule_equipment_show")
     * @Template()
     *
     * @param $id
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction($id)
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Equipment entity.');
      }

      $deleteForm = $this->createDeleteForm($id);

      return $this->render('fibeWWWConfBundle:Equipment:show.html.twig', array(
        'entity'      => $entity,
        'delete_form' => $deleteForm->createView(),
        'authorized'  => $authorization->getFlagSched()));
    }


    /**
     * Displays a form to edit an existing Equipment entity.
     *
     * @Route("/{id}/edit", name="schedule_equipment_edit")
     * @Template()
     *
     * @param $id
     *
     * @return Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction($id)
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      if (!$authorization->getFlagconfDatas())
      {
        throw new AccessDeniedException('Action not authorized !');
      }

      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Equipment entity.');
      }

      $editForm = $this->createForm(new EquipmentType(), $entity);
      $deleteForm = $this->createDeleteForm($id);

      return $this->render('fibeWWWConfBundle:Equipment:edit.html.twig', array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
        'authorized'  => $authorization->getFlagSched()
      ));
    }


    /**
     * Edits an existing Equipment entity.
     * @Route("/{id}/update", name="schedule_equipment_update")
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(Request $request, $id)
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      if (!$authorization->getFlagconfDatas())
      {
        throw new AccessDeniedException('Action not authorized !');
      }

      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Equipment entity.');
      }

      $deleteForm = $this->createDeleteForm($id);
      $editForm = $this->createForm(new EquipmentType(), $entity);
      $editForm->bind($request);

      if ($editForm->isValid())
      {
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('schedule_equipment_edit', array('id' => $id)));
      }

      return $this->render('fibeWWWConfBundle:Equipment:edit.html.twig', array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
        'authorized'  => $authorization->getFlagSched()
      ));
    }


    /**
     * Deletes a Equipment entity.
     * @Route("/{id}/delete", name="schedule_equipment_delete")
     * @Method({"POST", "DELETE"})
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {

      //Authorization Verification conference sched manager
      $user = $this->getUser();
      $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

      if (!$authorization->getFlagconfDatas())
      {
        throw new AccessDeniedException('Action not authorized !');
      }

      $form = $this->createDeleteForm($id);
      $form->bind($request);

      if ($form->isValid())
      {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id);

        if (!$entity)
        {
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
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
      return $this->createFormBuilder(array('id' => $id))
        ->add('id', 'hidden')
        ->getForm();
    }
  }
