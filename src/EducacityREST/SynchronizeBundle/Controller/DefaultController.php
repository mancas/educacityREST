<?php

namespace EducacityREST\SynchronizeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SynchronizeBundle:Default:index.html.twig', array('name' => $name));
    }
}
