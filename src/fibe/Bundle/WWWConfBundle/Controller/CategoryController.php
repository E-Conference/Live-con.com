<?php

/**
 *
 * @author :  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Category;
use fibe\Bundle\WWWConfBundle\Form\CategoryType;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
  /**
   * Lists all Category entities.
   *
   * @Route("/", name="schedule_category")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'Category');

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

    return array(
      'pager' => $pager,
    );
  }

  /**
   * Finds and displays a Category entity.
   *
   * @Route("/{id}/show", name="schedule_category_show")
   * @Template()
   */
  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Category', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Category entity.');
    }

    // $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity' => $entity,
      // 'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to create a new Category entity.
   *
   * @Route("/new", name="schedule_category_new")
   * @Template()
   */
  public function newAction()
  {
    // throw new ServiceUnavailableHttpException('Not available yet.');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Category');
    $form = $this->createForm(new CategoryType(), $entity);

    return array(
      'entity' => $entity,
      'form' => $form->createView(),
    );
  }

  /**
   * Creates a new Category entity.
   *
   * @Route("/create", name="schedule_category_create")
   */
  public function createAction(Request $request)
  {
    // throw new ServiceUnavailableHttpException('Not available yet.');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'Category');
    $form = $this->createForm(new CategoryType(), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity%[%id%] has been created',
          array(
            '%entity%' => 'Category',
            '%id%' => $entity->getId()
          )
        )
      );

      return $this->redirect($this->generateUrl('schedule_category_show', array('id' => $entity->getId())));
    }
    return $this->redirect($this->generateUrl('schedule_category_new'));
  }

  /**
   * Displays a form to edit an existing Category entity.
   *
   * @Route("/{id}/edit", name="schedule_category_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Category', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Category entity.');
    }

    $editForm = $this->createForm(new CategoryType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return array(
      'entity' => $entity,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Edits an existing Category entity.
   *
   * @Route("/{id}/update", name="schedule_category_update")
   * @Method("POST")
   * @Template("fibeWWWConfBundle:Category:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'Category', $id);

    if (!$entity)
    {
      throw $this->createNotFoundException('Unable to find Category entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new CategoryType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid())
    {
      $em->persist($entity);
      $em->flush();

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity%[%id%] has been updated',
          array(
            '%entity%' => 'Category',
            '%id%' => $entity->getId()
          )
        )
      );

      return $this->redirect($this->generateUrl('schedule_category_show', array('id' => $id)));
    }

    return array(
      'entity' => $entity,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Deletes a Category entity.
   *
   * @Route("/{id}/delete", name="schedule_category_delete")
   * @Method({"POST", "DELETE"})
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'Category', $id);

      if (!$entity)
      {
        throw $this->createNotFoundException('Unable to find Category entity.');
      }

      $em->remove($entity);
      $em->flush();

      $this->get('session')->getFlashBag()->add(
        'info',
        $this->get('translator')->trans(
          '%entity%[%id%] has been deleted',
          array(
            '%entity%' => 'Category',
            '%id%' => $id
          )
        )
      );
    }

    return $this->redirect($this->generateUrl('schedule_category'));
  }

  /**
   * Display Category deleteForm.
   *
   * @Template()
   */
  // public function deleteFormAction($id)
  // {
  //   $em = $this->getDoctrine()->getManager();
  //   $entity = $em->getRepository('fibeWWWConfBundle:Category')->find($id);

  //   if (!$entity)
  //   {
  //     throw $this->createNotFoundException('Unable to find Category entity.');
  //   }

  //   $deleteForm = $this->createDeleteForm($id);

  //   return array(
  //     'entity' => $entity,
  //     'delete_form' => $deleteForm->createView(),
  //   );
  // }

  private function createDeleteForm($id)
  {

    return $this->createFormBuilder(array('id' => $id))
      ->add('id', 'hidden')
      ->getForm();
  }
}
