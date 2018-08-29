<?php

namespace DondrekielAppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DondrekielAppBundle\Repository\MessageRepository;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dondrekiel_homepage")
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }

        $team = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('DondrekielAppBundle::default/index.html.twig', ['team' => $team]);
    }

    /**
     * @Route("/anleitung", name="dondrekiel_home_instructions")
     */
    public function instructionsAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('DondrekielAppBundle::default/instructions.html.twig');
    }

    /**
     * @Route("/nachrichten", name="dondrekiel_home_messages")
     */
    public function messagesAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('DondrekielAppBundle:Message')->findAll();

        return $this->render('DondrekielAppBundle::default/messages.html.twig', array(
            'messages' => $messages,
        ));
    }

    /**
     * @Route("/kontakt", name="dondrekiel_home_contact")
     */
    public function contactAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('DondrekielAppBundle::default/contact.html.twig');
    }
}
