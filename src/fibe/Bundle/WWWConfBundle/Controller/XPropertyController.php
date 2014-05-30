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
use fibe\Bundle\WWWConfBundle\Entity\CalendarEntity;
use fibe\Bundle\WWWConfBundle\Form\XPropertyType;
use fibe\Bundle\WWWConfBundle\Entity\XProperty;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Event controller.
 *
 * @Route("/xproperty")
 */
class XPropertyController extends Controller
{
  /**
   * Add a XProperty
   *
   * @Route("/{id}/add", name="schedule_xproperty_add")
   */
  public function addXPropertyAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $calendarEntity = $em->getRepository('fibeWWWConfBundle:CalendarEntity')->find($id);

    if (!$calendarEntity)
    {
      throw $this->createNotFoundException('Unable to find Calendar entity.');
    }

    $entity = new XProperty();
    $form = $this->createForm(new XPropertyType, $entity);
    $form->bind($request);

    if ($form->isValid())
    {
      $em->persist($entity);
      $em->flush();
    }
    else
    {
      die('todo: flash message');
    }

    return $this->redirect(
      $this->generateUrl(
        'schedule_event_edit',
        array('id' => $calendarEntity->getId())
      )
    );
  }
}
