<?php

namespace fibe\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
     * @Route("/")
     * 
     */
class DataController extends Controller
{
    /**
     * @Route("/{object}", name="list")
     * 
     */
    public function listAction($object)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('fibeWWWConfBundle:'.$object);

        if(!$repo){
            $repo = $em->getRepository('IDCISimpleScheduleBundle:'.$object);
        }
        
        $entities = $repo->findAll();

         return $this->render('DataBundle:'.$object.':list.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/{object}/{slug}", name="object")
     * @Template()
     */
    public function objectAction($object,$slug)
    {

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('fibeWWWConfBundle:'.$object);

         if(!$repo){
            $repo = $em->getRepository('IDCISimpleScheduleBundle:'.$object);
        }

         $entity = $repo->findOneBySlug($slug);

        return $this->render('fibeDataBundle:'.$object.':object.html.twig', array(
            'entity' => $entity,
        ));
    }

   
    
}
