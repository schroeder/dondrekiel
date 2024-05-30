<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'dondrekiel_homepage')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/anleitung', name: 'dondrekiel_home_instructions')]
    public function instructionsAction(Request $request)
    {
/*        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }*/
        // replace this example code with whatever you need
        return $this->render('default/instructions.html.twig');
    }

    #[Route('/nachrichten', name: 'dondrekiel_home_messages')]
    public function messagesAction(Request $request)
    {
     /*   if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }*/

        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('DondrekielAppBundle:Message')->findAll();

        return $this->render('default/messages.html.twig', array(
            'messages' => $messages,
        ));
    }

    #[Route('/kontakt', name: 'dondrekiel_home_contact')]
    public function contactAction(Request $request)
    {
        /*if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }*/
        // replace this example code with whatever you need
        return $this->render('default/contact.html.twig');
    }
}

