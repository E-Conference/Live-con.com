<?php

namespace fibe\UserDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use fibe\Bundle\WWWConfBundle\Entity\Paper;
use fibe\Bundle\WWWConfBundle\Form\PaperType;

/**
 * user data controller.
 *
 * @Route("/UserData")
 */
class UserDataController extends Controller
{
    /**
     * @Route("/",name="userData_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

   
}
