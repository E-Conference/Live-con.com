<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\Bundle\WWWConfBundle\Entity\Theme;
use fibe\Bundle\WWWConfBundle\Form\ThemeType;

/**
 * Theme controller.
 *
 * @Route("/theme")
 */
class ThemeController extends Controller
{
    /**
     * Lists all Theme entities.
     *
     * @Route("/", name="schedule_theme")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('fibeWWWConfBundle:Theme')->findAll();
        $currentConf = $this->getUser()->getCurrentConf();
        $entities = $currentConf->getThemes();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Theme entity.
     *
     * @Route("/", name="schedule_theme_create")
     * @Method("POST")
     * @Template("fibeWWWConfBundle:Theme:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Theme();
        $form = $this->createForm(new ThemeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConference($this->getUser()->getCurrentConf());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_theme_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Theme entity.
     *
     * @Route("/new", name="schedule_theme_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Theme();
        $form   = $this->createForm(new ThemeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Theme entity.
     *
     * @Route("/{id}", name="schedule_theme_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Theme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Theme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Theme entity.
     *
     * @Route("/{id}/edit", name="schedule_theme_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Theme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Theme entity.');
        }

        $editForm = $this->createForm(new ThemeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Theme entity.
     *
     * @Route("/{id}", name="schedule_theme_update")
     * @Method("PUT")
     * @Template("fibeWWWConfBundle:Theme:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('fibeWWWConfBundle:Theme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Theme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ThemeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_theme_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Theme entity.
     *
     * @Route("/{id}", name="schedule_theme_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('fibeWWWConfBundle:Theme')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Theme entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_theme'));
    }

    /**
     * Creates a form to delete a Theme entity by id.
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
