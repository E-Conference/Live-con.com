<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     *@Template()
     */
    public function indexAction(Request $request)
    {
        
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        $em = $this->getDoctrine()->getManager();

        $entities = $this->getUser()->getCurrentConf()->getEvents()->toArray();

        $adapter = new ArrayAdapter($entities);
        $pager = new PagerFanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('max_per_page'));

        try {
            $pager->setCurrentPage($request->query->get('page', 1));
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        //Form Filter
        $filters =$this->createForm(new ConfEventFilterType($this->getUser()));
        
        return array(
            'pager' => $pager,
            'authorized' => $authorization->getFlagSched(),
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
        $filters =$this->createForm(new ConfEventFilterType($this->getUser()));
        $filters->bindRequest($this->get('request'));

        if ($filters->isValid()) {
            // bind values from the request
          
             $entities = $em->getRepository('fibeWWWConfBundle:ConfEvent')->filtering($filters->getData(), $conf);
             $nbResult = count($entities);

             //Pager
             $adapter = new ArrayAdapter($entities);
             $pager = new PagerFanta($adapter);
             $pager->setMaxPerPage($this->container->getParameter('max_per_page'));
             try {
               $pager->setCurrentPage($request->query->get('page', 1));
             } catch (NotValidCurrentPageException $e) {
                throw new NotFoundHttpException();
             }

             return $this->render('fibeWWWConfBundle:ConfEvent:list.html.twig', array(
                 'pager'  => $pager,
                 'nbResult' => $nbResult,
             ));
        }

    }

    /**
     * Creates a new ConfEvent entity.
     *  @Route("/create", name="schedule_confevent_create")
     */
    public function createAction(Request $request)
    {
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
          } 

        //events are created via the schedule view only
        return $this->redirect($this->generateUrl('schedule_view'));

        $entity  = new ConfEvent();
        $form = $this->createForm(new ConfEventType($this->getUser(),$entity), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            //Link the new Event to the current Conf 
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity); 
            $em->flush();

       /*   $xprop= new XProperty(); 
            $xprop->setXNamespace("event_uri"); 
            $xprop->setXKey(rand(0,999999));
            $xprop->setXValue("http://dataconf-event/" . $entity->getId());  
            $xprop->setCalendarEntity($entity); 
            
            $em->persist($xprop); 

            $em->flush(); */

            return $this->redirect($this->generateUrl('schedule_confevent_show', array('id' => $entity->getId())));
        }

        return $this->render('fibeWWWConfBundle:ConfEvent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagSched()
        ));
    }

    /**
     * Displays a form to create a new ConfEvent entity.
     * @Route("/new",name="schedule_confevent_new") 
     * @Template()
     */
    public function newAction()
    { 
        //events are created via the schedule view only
        return $this->redirect($this->generateUrl('schedule_view'));
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
          } 
        $entity = new ConfEvent();
        $form   = $this->createForm(new ConfEventType($this->getUser(),$entity), $entity);


        return $this->render('fibeWWWConfBundle:ConfEvent:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorization->getFlagSched()
        ));
    }

    /**
     * Finds and displays a ConfEvent entity.
     * @Route("/{id}/show", name="schedule_confevent_show")
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
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:ConfEvent:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(), 
            'authorized' => $authorization->getFlagSched()       

            ));
    }

    /**
     * Displays a form to edit an existing ConfEvent entity.
     * @Route("/{id}/edit", name="schedule_confevent_edit")
     * @Template()
     */
    public function editAction($id)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $em = $this->getDoctrine()->getManager();

        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }

        $role = new Role();
        $roleForm = $this->createForm(new RoleType($this->getUser()), $role);
        $editForm = $this->createForm(new ConfEventType($this->getUser(),$entity), $entity);

        $papersForSelect = $this->getUser()->getCurrentConf()->getPapers()->toArray();
        $form_paper = $this->createFormBuilder($entity)
            ->add('papers', 'entity', array(
                      'class'    => 'fibeWWWConfBundle:Paper',
                      'property' => 'title',
                      'required' => false,
                      'multiple' => false,
                      'choices'=> $papersForSelect,
                      'label'    => "Select paper"))
            ->getForm();

         $topicsForSelect = $this->getUser()->getCurrentConf()->getTopics()->toArray();
         $form_topic = $this->createFormBuilder($entity)
            ->add('topics', 'entity', array(
                  'class'    => 'fibeWWWConfBundle:Topic',
                  'required' => false,
                  'property' => 'name',
                  'multiple' => false,
                  'choices'=> $topicsForSelect,
                  'label'    => "Select topic" ))
            ->getForm();

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('fibeWWWConfBundle:ConfEvent:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'role_form'    => $roleForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'paper_form' => $form_paper->createView(),
            'topic_form' => $form_topic->createView(),
            'authorized' => $authorization->getFlagSched(),
        ));
    }

    /**
     * Edits an existing ConfEvent entity.
     *  @Route("/{id}/update", name="schedule_confevent_update")
     *  @Template("fibeWWWConfBundle:ConfEvent:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $em = $this->getDoctrine()->getManager();
         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }
 

        $form = $this->createForm(new ConfEventType($this->getUser(),$entity), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //If is a main confEvent => have to update the slug conference
            if($entity->getIsMainConfEvent()){

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
        
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

             //The object have to belongs to the current conf
            $currentConf=$this->getUser()->getCurrentConf();
            $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ConfEvent entity.');
            }

            $children = $entity->getChildren();
            $mainConfEvent = $this->getUser()->getCurrentConf()->getMainConfEvent(); 

            if($mainConfEvent->getId() ==  $entity->getId()){
                $this->container->get('session')->getFlashBag()->add(
                     'error',
                     'Sorry, you cannot delete the Conference Event'
                     );
                return $this->redirect($this->generateUrl('schedule_event'));
            }
            foreach($children as $child)
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

        return $this->redirect($this->generateUrl('schedule_event'));
    }

    /**
     * Creates a form to delete a ConfEvent entity by id.
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

    /*************************************** topics *****************************************************/

    /**
     * Add topic to a confEvent
     *  @Route("/addTopic", name="schedule_confevent_addTopic")
     *  @Method("POST")
     *  
     */
    public function addTopicAction(Request $request)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_topic = $request->request->get('id_topic');
        $id_entity = $request->request->get('id_entity');
           
        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $topic =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id_topic));

         if (!$entity || !$topic) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity or topic.');
        }

        //Add paper to the confEvent
        $entity->addTopic($topic);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:topicRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }


     /**
     * Delete topic of a confEvent
     *  @Route("/deleteTopic", name="schedule_confevent_deleteTopic")
     *  @Method("POST")
     *  
     */
    public function deleteTopicAction(Request $request)
    {
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_topic = $request->request->get('id_topic');
        $id_entity = $request->request->get('id_entity');
           
        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $topic =  $em->getRepository('fibeWWWConfBundle:Topic')->findOneBy(array('conference' => $currentConf, 'id' => $id_topic));

         if (!$entity || !$topic) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity.');
        }
        //Delete topic to the confEvent
        $entity->removeTopic($topic);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:topicRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }


    /*************************************** papers *****************************************************/

     /**
     * Add paper to the confEvent
     *  @Route("/addPaper", name="schedule_confevent_addPaper")
     *  @Method("POST")
     *  
     */
    public function addPaperAction(Request $request)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_paper = $request->request->get('id_paper');
        $id_entity = $request->request->get('id_entity');

        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $paper =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id_paper));

         if (!$entity || !$paper) {
                throw $this->createNotFoundException('Unable to find ConfEvent entity or paper.');
        }

        //Add paper to the confEvent
        $entity->addPaper($paper);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:paperRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }

     /**
     * Delete paper to a confEvent
     *  @Route("/deletePaper", name="schedule_confevent_deletePaper")
     *  @Method({"DELETE","POST"})
     *  
     */
    public function deletePaperAction(Request $request)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_paper = $request->request->get('id_paper');
        $id_entity = $request->request->get('id_entity');
           
        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $paper =  $em->getRepository('fibeWWWConfBundle:Paper')->findOneBy(array('conference' => $currentConf, 'id' => $id_paper));
         if (!$entity || !$paper) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity or paper.');
        }

        //Add paper to the confEvent
        $entity->removePaper($paper);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:paperRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }

    /*************************************** person *****************************************************/

     /**
     * Add person to the confEvent
     * 
     *  @Route( "/addPerson",name="schedule_confevent_addPerson")
     *  @Method("POST")
     *  
     */
    public function addPersonAction(Request $request)
    {      
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_person = $request->request->get('id_person');
        $id_type = $request->request->get('id_type');
        $id_entity = $request->request->get('id');
     
        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $type = $em->getRepository('fibeWWWConfBundle:RoleType')->find($id_type);
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $person =  $em->getRepository('fibeWWWConfBundle:Person')->findOneBy(array('conference' => $currentConf, 'id' => $id_person));

         if (!$entity || !$person || !$type) {
                throw $this->createNotFoundException('Unable to find ConfEvent entity, person or type.');
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

        return $this->render('fibeWWWConfBundle:ConfEvent:personRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }

    /**
     * Delete person  to a confEvent
     *  @Route("/deletePerson", name="schedule_confevent_deletePerson")
     *  @Method("POST")
     *  
     */
    public function deletePersonAction(Request $request)
    {
       
          //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

        if(!$authorization->getFlagSched()){
            throw new AccessDeniedException('Action not authorized !');
        } 

        $id_role = $request->request->get('id_role');
        $id_entity = $request->request->get('id_entity');
           
        $em = $this->getDoctrine()->getManager();

        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('fibeWWWConfBundle:ConfEvent')->findOneBy(array('conference' => $currentConf, 'id' => $id_entity));
        $role =  $em->getRepository('fibeWWWConfBundle:Role')->findOneBy(array('conference' => $currentConf, 'id' => $id_role));

         if (!$entity || !$role) {
            throw $this->createNotFoundException('Unable to find ConfEvent entity or role.');
        }

        //Add role to the confEvent
        $entity->removeRole($role);
        $em->remove($role);
        //Sauvegarde des données
        $em->persist($entity);
        $em->flush();

        return $this->render('fibeWWWConfBundle:ConfEvent:personRelation.html.twig', array(
            'entity'  => $entity,
            'authorized' => $authorization->getFlagSched()
        ));
    }
}
