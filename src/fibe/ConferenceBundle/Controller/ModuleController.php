<?php

namespace fibe\ConferenceBundle\Controller;

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
 * @Route("/admin/module")
 */
class ModuleController extends Controller
{
	/**
	 * @Route("/edit", name="conference_module_index")
	 * 
	 * @Template()
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('fibeWWWConfBundle:Module')->findAll();

        return  array(
            'entities' => $entities,
        );
    }

    /**
	 * @Route("/create", name="conference_module_create")
	 * 
	 * @Template()
	 */
    public function createAction(Request $request)
    {
        $entity  = new Module();
        $form = $this->createForm(new ModuleType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('module_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
	 * @Route("/new", name="conference_module_new")
	 * 
	 * @Template()
	 */
    public function newAction()
    {
        $entity = new Module();
        $form   = $this->createForm(new ModuleType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
	 * @Route("/new", name="conference_module_show")
	 * 
	 * @Template()
	 */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Module entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),       
        );
    }

    /**
	 * @Route("/edit", name="conference_module_edit")
	 * 
	 * @Template()
	 */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Module entity.');
        }

        $editForm = $this->createForm(new ModuleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Module entity.
     * @Route("{id}/module", name="conference_module_update")
     * 
     *
     */
    public function updateAction(Request $request, $id)
    {
        
         //Authorization Verification conference datas manager
        $user=$this->getUser();
        $authorization = $user->getAuthorizationByConference($user->getCurrentConf());

         if(!$authorization->getFlagconfDatas()){
            //throw new AccessDeniedException('Action not authorized !');
           return $this->redirect($this->generateUrl('schedule_conference_show'));
          } 

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Module entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ModuleType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->container->get('session')->getFlashBag()->add(
                  'success',
                  'The module is succesfully updated'
              );
        }else{

            $this->container->get('session')->getFlashBag()->add(
                'error',
                'The module cannot be saved'
            );
        }

        return $this->redirect($this->generateUrl('schedule_conference_settings'));
    }

    /**
	 * @Route("/delete", name="conference_module_delete")
	 * 
	 * @Template()
	 */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:Module')->find($id);

            if (!$entity) {
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
