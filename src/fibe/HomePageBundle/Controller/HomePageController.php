<?php

namespace fibe\HomePageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use fibe\HomePageBundle\Form\ContactType;


/**
 * Class HomePageController
 * @package fibe\HomePageBundle\Controller
 */
class HomePageController extends Controller
{
  /**
   * @Route("/", name="homePage_index")
   * @Template()
   */
  public function indexAction(Request $request)
  {
    $form = $this->createForm(new ContactType());

    $em = $this->getDoctrine()->getManager();
    $conferences = $em->getRepository('fibeWWWConfBundle:WwwConf')->findOrderByDate();

    if ($request->isMethod('POST'))
    {
      $form->bind($request);

      //if ($form->isValid()) {
      $message = \Swift_Message::newInstance()
        //->setSubject($form->get('subject')->getData())
        ->setSubject('New admin account Livecon')
        ->setFrom($form->get('email')->getData())
        ->setTo('flepeutrec@gmail.com')
        ->setBody(
          $this->renderView(
            'fibeHomePageBundle:Mail:contact.html.twig',
            array(
              'ip'       => $request->getClientIp(),
              'nom'      => $form->get('nom')->getData(),
              'prenom'   => $form->get('prenom')->getData(),
              'confname' => $form->get('confname')->getData(),
              'message'  => $form->get('message')->getData(),
              'email'    => $form->get('email')->getData(),
            )
          )
        );
      //$message->embed(Swift_Image::fromPath("http://localhost/Livecon/web/img/livecon.png"));

      $this->get('mailer')->send($message);

      $request->getSession()->getFlashBag()->add('success', 'Your email has been sent! Thanks!');

      return $this->redirect($this->generateUrl('homePage_index'));
      //}
    }

    return array(
      'form_contact' => $form->createView(),
      'conferences'  => $conferences
    );

  }
}