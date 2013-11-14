<?php

namespace fibe\Bundle\WWWConfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DocumentationController extends Controller
{
    /**
     * @Route("/documentation/{anchor}", name="documentation")
     * @Template()
     */
    public function documentationAction($anchor)
    {
        return $this->render('fibeWWWConfBundle:Documentation:documentation.html.twig', array(
            'anchor' => $anchor,
        ));

    }

}
