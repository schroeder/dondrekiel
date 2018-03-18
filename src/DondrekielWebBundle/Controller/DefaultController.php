<?php

namespace DondrekielWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DondrekielWebBundle:default:index.html.twig');
    }
}
