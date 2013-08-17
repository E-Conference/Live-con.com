<?php

namespace fibe\UserDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserDataController extends Controller
{
    /**
     * @Route("/UserData") name="userData_index"
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
