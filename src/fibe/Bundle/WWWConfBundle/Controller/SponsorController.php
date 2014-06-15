<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use fibe\Bundle\WWWConfBundle\Entity\Sponsor;
use fibe\Bundle\WWWConfBundle\Form\Filters\SponsorFilterType;
use fibe\Bundle\WWWConfBundle\Form\SponsorType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//Filter form type

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Topic controller.
 *
 * @Route("/sponsor")
 */
class SponsorController extends Controller
{
  /**
   * Lists all Sponsor entities.
   *
   * @Route("/", name="schedule_sponsor")
   * @Method("GET")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    //Authorization Verification conference datas manager
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'Sponsor');

    $adapter = new ArrayAdapter($entities);
    $pager = new PagerFanta($adapter);
    $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

    try
    {
      $pager->setCurrentPage($request->query->get('page', 1));
    } catch (NotValidCurrentPageException $e)
    {
      throw new NotFoundHttpException();
    }

    $filters = $this->createForm(new SponsorFilterType($this->getUser()));

    return [
      'pager'        => $pager,
      'filters_form' => $filters->createView(),
    ];
  }


  /**
   * Filter paper index list
   * @Route("/filter", name="schedule_sponsor_filter")
   */
  public function filterAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $conf = $this->getUser()->getCurrentConf();
    //Filters
    $filters = $this->createForm(new SponsorFilterType($this->getUser()));

    $filters->submit($this->get('request'));

    if ($filters->isValid())
    {
      // bind values from the request

      $entities = $em->getRepository('fibeWWWConfBundle:Sponsor')->filtering($filters->getData(), $conf);
      $nbResult = count($entities);

      //Pager
      $adapter = new ArrayAdapter($entities);
      $pager = new PagerFanta($adapter);
      $pager->setMaxPerPage($this->container->getParameter('max_per_page'));
      try
      {
        $pager->setCurrentPage($request->query->get('page', 1));
      } catch (NotValidCurrentPageException $e)
      {
        throw new NotFoundHttpException();
      }

      return $this->render(
        'fibeWWWConfBundle:Sponsor:list.html.twig',
        [
          'pager'    => $pager,
          'nbResult' => $nbResult,
        ]
      );
    }

  }

  /**
   * Creates a new Sponsor entity.
   *
   * @Route("/", name="schedule_sponsor_create")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Sponsor:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Sponsor');
    $form = $this->createForm(new SponsorType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity->setConference($this->getUser()->getCurrentConf());
      $em->persist($entity);
      $entity->uploadLogo();
      $em->flush();

      return $this->redirect($this->generateUrl('schedule_sponsor'));
    }

    return [
      'entity' => $entity,
      'form'   => $form->createView()
    ];
  }

  /**
   * Displays a form to create a new Sponsor entity.
   *
   * @Route("/new", name="schedule_sponsor_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Sponsor');
    $form = $this->createForm(new SponsorType(), $entity);

    return [
      'entity' => $entity,
      'form'   => $form->createView()
    ];
  }

  /**
   * Finds and displays a Sponsor entity.
   *
   * @Route("/{id}", name="schedule_sponsor_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Sponsor', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Topic entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'delete_form' => $deleteForm->createView()
    ];
  }

  /**
   * Displays a form to edit an existing Sponsor entity.
   *
   * @Route("/{id}/edit", name="schedule_sponsor_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Sponsor', $id);
    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Topic entity.');
    }

    $editForm = $this->createForm(new SponsorType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return [
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    ];
  }

  /**
   * Edits an existing Sponsor entity.
   *
   * @Route("/{id}", name="schedule_sponsor_update")
   * @Method("PUT")
   * @Template("fibeWWWConfBundle:Sponsor:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Sponsor', $id);
    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Topic entity.');
    }

    $em = $this->getDoctrine()->getManager();

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new SponsorType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $entity->uploadLogo();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('schedule_sponsor'));
    }

    return [
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView()
    ];
  }

  /**
   * Deletes a Sponsor entity.
   *
   * @Route("/{id}", name="schedule_sponsor_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Sponsor', $id);
      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Topic entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('schedule_sponsor'));
  }

  /**
   * Creates a form to delete a Sponsor entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(['id' => $id])
      ->add('id', 'hidden')
      ->getForm();
  }
}
