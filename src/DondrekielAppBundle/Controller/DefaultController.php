<?php

namespace DondrekielAppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('DondrekielAppBundle::default/index.html.twig');
    }
}
