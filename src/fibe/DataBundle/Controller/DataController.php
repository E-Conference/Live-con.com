<?php

namespace fibe\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 *
 */
class DataController extends Controller
{


  /*
    @Route("/{object}", name="list")


  public function listAction($object)
  {
      $em = $this->getDoctrine()->getManager();

      $repo = $em->getRepository('fibeWWWConfBundle:'.$object);

      if(!$repo){
          $repo = $em->getRepository('fibeWWWConfBundle:'.$object);
      }

      $entities = $repo->findAll();

       return $this->render('DataBundle:'.$object.':list.html.twig', array(
          'entities' => $entities,
      ));
  }
   */

  /*
   * @Route("/{object}/{slug}", name="object")
   * @Template()
   *
  public function objectAction($object,$slug)
  {

      $em = $this->getDoctrine()->getManager();

      $repo = $em->getRepository('fibeWWWConfBundle:'.$object);

       if(!$repo){
          $repo = $em->getRepository('fibeWWWConfBundle:'.$object);
      }

       $entity = $repo->findOneBySlug($slug);

      return $this->render('fibeDataBundle:'.$object.':object.html.twig', array(
          'entity' => $entity,
      ));
  }*/

  /**
   * @Route("/conferences", name="datas_conferences_list")
   *
   */
  public function conferencesAction()
  {
    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('fibeWWWConfBundle:WwwConf')->findAll();


    return $this->render('DataBundle:Conference:conferences.html.twig', array(
      'entities' => $entities,
    ));
  }


  /**
   * @Route("/filter", name="datas_conferences_filter")
   *
   */
  public function conferencesFilterAction(Request $request)
  {
    $name = $request->request->get('name');
    $em = $this->getDoctrine()->getManager();

    if (!empty($name))
    {
      $entities = $em->getRepository('fibeWWWConfBundle:WwwConf')->findByName($name);
    }
    else
    {
      $entities = $em->getRepository('fibeWWWConfBundle:WwwConf')->findAll();
    }


    return $this->render('DataBundle:Conference:conferencesList.html.twig', array(
      'entities' => $entities,
    ));
  }


}
