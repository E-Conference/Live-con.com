<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\SimpleScheduleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use IDCI\Bundle\SimpleScheduleBundle\Entity\Location;
use IDCI\Bundle\SimpleScheduleBundle\Form\LocationType;
use fibe\Bundle\WWWConfBundle\Entity\Equipment;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Location controller.
 *
 * @Route("/location")
 */
class LocationController extends Controller
{
    /**
     * Lists all Location entities.
     *
     * @Route("/", name="schedule_location")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        
         //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

        $em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('IDCISimpleScheduleBundle:Location')->findAll();
        $currentConf = $this->getUser()->getCurrentConf();
        $entities = $currentConf->getLocations()->toArray();
     
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
            'authorized' => $authorized,
        );
    }

    /**
     * Finds and displays a Location entity.
     *
     * @Route("/{id}/show", name="schedule_location_show")
     * @Template()
     */
    public function showAction($id)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

        $em = $this->getDoctrine()->getManager();

        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }
 
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity, 
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorized, 
        );
    }

    /**
     * Displays a form to create a new Location entity.
     *
     * @Route("/new", name="schedule_location_new")
     * @Template()
     */
    public function newAction()
    {
        
       //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity = new Location();
        $form   = $this->createForm(new LocationType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorized,
            'authorized' => $authorized,
        );
    }

    /**
     * Creates a new Location entity.
     *
     * @Route("/create", name="schedule_location_create")
     * @Method("POST")
     * @Template("IDCISimpleScheduleBundle:Location:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $entity  = new Location();
        $form = $this->createForm(new LocationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'info',
                $this->get('translator')->trans('%entity%[%id%] has been created', array(
                    '%entity%' => 'Location',
                    '%id%'     => $entity->getId()
                ))
            );

            return $this->redirect($this->generateUrl('schedule_location_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'authorized' => $authorized,
        );
    }

    /**
     * Displays a form to edit an existing Location entity.
     *
     * @Route("/{id}/edit", name="schedule_location_edit")
     * @Template()
     */
    public function editAction($id)
    { 
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();
         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $editForm = $this->createForm(new LocationType(), $entity); 
        $deleteForm = $this->createDeleteForm($id);

        $equipments = $em->getRepository('fibeWWWConfBundle:Equipment')->getEquipmentForLocationSelect($entity);
    
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(), 
            'delete_form' => $deleteForm->createView(),
            'equipments'  => $equipments,
            'authorized'  => $authorized,
        );
    }

    /**
     * Edits an existing Location entity.
     *
     * @Route("/{id}/update", name="schedule_location_update")
     * @Method("POST")
     * @Template("IDCISimpleScheduleBundle:Location:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();
         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }
 
        $editForm = $this->createForm(new LocationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

        $this->get('session')->getFlashBag()->add(
            'info',
            $this->get('translator')->trans('%entity%[%id%] has been updated', array(
                '%entity%' => 'Location',
                '%id%'     => $entity->getId()
            ))
        );

        return $this->redirect($this->generateUrl('schedule_location_edit', array('id' => $id)));
        
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(), 
            'authorized' => $authorized,
        );
    }

    /**
     * Deletes a Location entity.
     *
     * @Route("/addEquipment", name="schedule_location_addEquipment")
     * @Method("POST")
     */
    public function addEquipmentAction(Request $request)
    {
        
       //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $id_location = $request->request->get('id_location');
        $id_equipment = $request->request->get('id_equipment');

        $em = $this->getDoctrine()->getManager();

      
          //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $location =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id_location));
        $equipment  = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id_equipment);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location or equipment entity.');
        }

        $equipments = $em->getRepository('fibeWWWConfBundle:Equipment')->getEquipmentForLocationSelect($location);

        $location->addEquipment($equipment);

        $em->persist($location);
        $em->flush();

          return $this->render('IDCISimpleScheduleBundle:Location:equipmentsRelation.html.twig', array(
            'entity'  => $location,
            'equipments' => $equipments,
            'authorized' => $authorized,
        ));

    }


    /**
     * Deletes a Location entity.
     *
     * @Route("/deleteEquipment", name="schedule_location_deleteEquipment")
     * @Method("POST")
     */
    public function deleteEquipmentAction(Request $request)
    {
        
       //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $id_location = $request->request->get('id_location');
        $id_equipment = $request->request->get('id_equipment');

        $em = $this->getDoctrine()->getManager();
         //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $location =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id_location));
        $equipment  = $em->getRepository('fibeWWWConfBundle:Equipment')->find($id_equipment);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location or equipment entity.');
        }

        $location->removeEquipment($equipment);

        $em->persist($location);
        $em->flush();

        $equipments = $em->getRepository('fibeWWWConfBundle:Equipment')->getEquipmentForLocationSelect($location);


          return $this->render('IDCISimpleScheduleBundle:Location:equipmentsRelation.html.twig', array(
            'entity'  => $location,
            'equipments' => $equipments,
            'authorized' => $authorized,
        ));

    }

    /**
     * Deletes a Location entity.
     *
     * @Route("/{id}/delete", name="schedule_location_delete")
     * @Method({"POST", "DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
             //The object have to belongs to the current conf
            $currentConf=$this->getUser()->getCurrentConf();
            $entity =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Location entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add(
                'info',
                $this->get('translator')->trans('%entity%[%id%] has been deleted', array(
                    '%entity%' => 'Location',
                    '%id%'     => $id
                ))
            );
        }

        return $this->redirect($this->generateUrl('schedule_location'));
    }
    
    /**
     * Display Location deleteForm.
     * 
     * @Template()
     */
    public function deleteFormAction($id)
    {
        
        //Authorization Verification conference sched manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());
        $authorized = ($authorization->getFlagconfDatas() || $authorization->getFlagSched());

         if(!$authorized){
            throw new AccessDeniedException('Action not authorized !');
          }

        $em = $this->getDoctrine()->getManager();
        //The object have to belongs to the current conf
        $currentConf=$this->getUser()->getCurrentConf();
        $entity =  $em->getRepository('IDCISimpleScheduleBundle:Location')->findOneBy(array('conference' => $currentConf, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Location entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'authorized' => $authorized,
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
