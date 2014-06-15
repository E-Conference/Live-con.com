<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
use fibe\Bundle\WWWConfBundle\Form\ConfEventType;
use fibe\Bundle\WWWConfBundle\Entity\Role;
use fibe\Bundle\WWWConfBundle\Entity\Topic;
use fibe\Bundle\WWWConfBundle\Form\RoleType as RoleType;
use fibe\Bundle\WWWConfBundle\Form\TopicType as TopicType;
//Filter type form
use fibe\Bundle\WWWConfBundle\Form\Filters\ConfEventFilterType;

use fibe\Bundle\WWWConfBundle\Entity\XProperty;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ConfEvent controller.
 * @Route("/confevent")
 */
class ConfEventController extends Controller
{
  /**
   * Lists all ConfEvent entities.
   * @Route("/",name="schedule_confevent")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $entities = $this->get('fibe_security.acl_entity_helper')->getEntitiesACL('VIEW', 'ConfEvent');

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

    //Form Filter
    $filters = $this->createForm(new ConfEventFilterType($this->getUser()));

    return array(
      'pager'        => $pager,
      'filters_form' => $filters->createView()
    );
  }

  /**
   * Filter confevent
   * @Route("/filter", name="schedule_confevent_filter")
   */
  public function filterAction(Request $request)
  {

    $em = $this->getDoctrine()->getManager();

    $conf = $this->getUser()->getCurrentConf();
    //Filters
    $filters = $this->createForm(new ConfEventFilterType($this->getUser()));
    $filters->submit($request);

    if ($filters->isValid())
    {
      // bind values from the request

      $entities = $em->getRepository('fibeWWWConfBundle:ConfEvent')->filtering($filters->getData(), $conf);
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
        'fibeWWWConfBundle:ConfEvent:list.html.twig',
        array(
          'pager'    => $pager,
          'nbResult' => $nbResult,
        )
      );
    }
  }

  /**
   * Creates a new ConfEvent entity.
   * @Route("/create", name="schedule_confevent_create")
   */
  public function createAction(Request $request)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'ConfEvent');

    $form = $this->createForm(new ConfEventType($this->getUser()), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $entity->setConference($this->getUser()->getCurrentConf());
      $em->persist($entity);

      // $xprop= new XProperty();
      // $xprop->setXNamespace("event_uri");
      // $xprop->setXKey(rand(0,999999));
      // $xprop->setXValue("http://dataconf-event/" . $entity->getId());
      // $xprop->setCalendarEntity($entity);
      // $em->persist($xprop);

      $em->flush();

      //$this->get('fibe_security.acl_entity_helper')->createACL($entity,MaskBuilder::MASK_OWNER);

      return $this->redirect($this->generateUrl('schedule_confevent_show', array('id' => $entity->getId())));
    }

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:new.html.twig',
      array(
        'entity' => $entity,
        'form'   => $form->createView(),
      )
    );
  }

  /**
   * Displays a form to create a new ConfEvent entity.
   * @Route("/new",name="schedule_confevent_new")
   * @Template()
   */
  public function newAction()
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('CREATE', 'ConfEvent');

    $form = $this->createForm(new ConfEventType($this->getUser()), $entity);

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:new.html.twig',
      array(
        'entity' => $entity,
        'form'   => $form->createView(),
      )
    );
  }

  /**
   * Finds and displays a ConfEvent entity.
   * @Route("/{id}/show", name="schedule_confevent_show")
   * @Template()
   */

  public function showAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'ConfEvent', $id);

    $deleteForm = $this->createDeleteForm($id);

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:show.html.twig',
      array(
        'entity'      => $entity,
        'delete_form' => $deleteForm->createView(),

      )
    );
  }

  /**
   * Displays a form to edit an existing ConfEvent entity.
   * @Route("/{id}/edit", name="schedule_confevent_edit")
   * @Template()
   */
  public function editAction($id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $editForm = $this->createForm(new ConfEventType($this->getUser()), $entity);
    $deleteForm = $this->createDeleteForm($id);

    $currentConf = $this->getUser()->getCurrentConf();
    $form_role = $this->createForm(new RoleType($this->getUser()), new Role());
    $papersForSelect = $currentConf->getPapers()->toArray();
    $form_paper = $this->createFormBuilder($entity)
      ->add(
        'papers',
        'entity',
        array(
          'class'    => 'fibeWWWConfBundle:Paper',
          'property' => 'title',
          'required' => false,
          'multiple' => false,
          'choices'  => $papersForSelect,
          'label'    => "Select paper"
        )
      )
      ->getForm();

    $topicsForSelect = $currentConf->getTopics()->toArray();
    $form_topic = $this->createFormBuilder($entity)
      ->add(
        'topics',
        'entity',
        array(
          'class'    => 'fibeWWWConfBundle:Topic',
          'required' => false,
          'property' => 'name',
          'multiple' => false,
          'choices'  => $topicsForSelect,
          'label'    => "Select topic"
        )
      )
      ->getForm();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:edit.html.twig',
      array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
        'role_form'   => $form_role->createView(),
        'paper_form'  => $form_paper->createView(),
        'topic_form'  => $form_topic->createView(),
      )
    );
  }

  /**
   * Edits an existing ConfEvent entity.
   * @Route("/{id}/update", name="schedule_confevent_update")
   * @Template("fibeWWWConfBundle:ConfEvent:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $form = $this->createForm(new ConfEventType($this->getUser(), $entity), $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      //If is a main confEvent => have to update the slug conference
      if ($entity->getIsMainConfEvent())
      {

        $conference = $entity->getConference();
        $conference->slugify();
        $em->persist($conference);
      }

      $em->persist($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('schedule_confevent_show', array('id' => $id)));
  }

  /**
   * Deletes a ConfEvent entity.
   * @Route("/{id}/delete", name="schedule_confevent_delete")
   * @Method({"DELETE","POST"})
   */
  public function deleteAction(Request $request, $id)
  {
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('DELETE', 'ConfEvent', $id);

    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()->getManager();

      $mainConfEvent = $this->getUser()->getCurrentConf()->getMainConfEvent();
      if ($mainConfEvent->getId() == $entity->getId())
      {
        $this->container->get('session')->getFlashBag()->add(
          'error',
          'You cannot delete the Conference Event'
        );

        return $this->redirect($this->generateUrl('schedule_confevent_edit', array('id' => $entity->getId())));
      }
      // set orphan children as children of main conf event
      $children = $entity->getChildren();
      foreach ($children
               as
               $child)
      {
        $child->setParent($mainConfEvent);
        $em->persist($child);
      }
      $this->container->get('session')->getFlashBag()->add(
        'success',
        'Event successfully deleted ! \n Its children have been set as children of the conference'
      );

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('schedule_confevent'));
  }

  /**
   * Creates a form to delete a ConfEvent entity by id.
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









  /*************************************** topics *****************************************************/

  /**
   * Add topic to a confEvent
   * @Route("/addTopic", name="schedule_confevent_addTopic")
   * @Method("POST")
   *
   */
  public function addTopicAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $id_topic = $request->request->get('id_topic');
    $topic = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Topic', $id_topic);

    $em = $this->getDoctrine()->getManager();
    //Add paper to the confEvent
    $entity->addTopic($topic);
    //Sauvegarde des données
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:topicRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }


  /**
   * Delete topic of a confEvent
   * @Route("/deleteTopic", name="schedule_confevent_deleteTopic")
   * @Method("POST")
   *
   */
  public function deleteTopicAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $id_topic = $request->request->get('id_topic');
    $topic = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Topic', $id_topic);

    //Delete topic to the confEvent
    $entity->removeTopic($topic);
    //Sauvegarde des données
    $em = $this->getDoctrine()->getManager();
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:topicRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }


  /*************************************** papers *****************************************************/

  /**
   * Add paper to the confEvent
   * @Route("/addPaper", name="schedule_confevent_addPaper")
   * @Method("POST")
   *
   */
  public function addPaperAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $id_paper = $request->request->get('id_paper');
    $paper = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Paper', $id_paper);

    //Add paper to the confEvent
    $entity->addPaper($paper);
    //Sauvegarde des données
    $em = $this->getDoctrine()->getManager();
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:paperRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }

  /**
   * Delete paper to a confEvent
   * @Route("/deletePaper", name="schedule_confevent_deletePaper")
   * @Method({"DELETE","POST"})
   *
   */
  public function deletePaperAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $id_paper = $request->request->get('id_paper');
    $paper = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Paper', $id_paper);

    //Add paper to the confEvent
    $entity->removePaper($paper);
    //Sauvegarde des données
    $em = $this->getDoctrine()->getManager();
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:paperRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }

  /*************************************** person *****************************************************/

  /**
   * Add person to the confEvent
   *
   * @Route( "/addPerson",name="schedule_confevent_addPerson")
   * @Method("POST")
   *
   */
  public function addPersonAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $id_person = $request->request->get('id_person');
    $person = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Person', $id_person);

    $id_type = $request->request->get('id_type');

    $em = $this->getDoctrine()->getManager();
    $type = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id_type);

    if (!$type)
    {
      throw $this->createNotFoundException('Unable to find type.');
    }

    $role = new Role();
    $role->setPerson($person);
    $role->setType($type);
    $role->setEvent($entity);
    $role->setConference($this->getUser()->getCurrentConf());
    $em->persist($role);

    //Add paper to the confEvent
    $entity->addRole($role);
    //Sauvegarde des données
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:personRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }

  /**
   * Delete person  to a confEvent
   * @Route("/deletePerson", name="schedule_confevent_deletePerson")
   * @Method("POST")
   *
   */
  public function deletePersonAction(Request $request)
  {
    $id = $request->request->get('id_entity');
    $entity = $this->get('fibe_security.acl_entity_helper')->getEntityACL('EDIT', 'ConfEvent', $id);

    $em = $this->getDoctrine()->getManager();

    $id_role = $request->request->get('id_role');
    $role = $em->getRepository('fibeWWWConfBundle:Role')->findOneBy(
      array('conference' => $this->getUser()->getCurrentConf(), 'id' => $id_role)
    );

    $em = $this->getDoctrine()->getManager();
    //Add role to the confEvent
    $entity->removeRole($role);
    $em->remove($role);
    //Sauvegarde des données
    $em->persist($entity);
    $em->flush();

    return $this->render(
      'fibeWWWConfBundle:ConfEvent:personRelation.html.twig',
      array(
        'entity' => $entity,
      )
    );
  }
}
