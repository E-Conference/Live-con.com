<?php

namespace fibe\DataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DataController extends Controller
{
    /**
     * @Route("/persons")
     * @Template()
     */
    public function personListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentConf = $this->getUser()->getCurrentConf();
        $entities = $currentConf->getPersons();

        return $this->render('DataBundle:Default:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/person/{id}")
     * @Template()
     */
    public function personAction($id)
    {
    }

}
