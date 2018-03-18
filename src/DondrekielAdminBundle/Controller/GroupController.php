<?php

namespace DondrekielAdminBundle\Controller;

use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Message;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Group controller.
 *
 * @Route("admin/group")
 */
class GroupController extends Controller
{
    /**
     * Lists all game entities.
     *
     * @Route("/", name="group_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groups = $em->getRepository('DondrekielAdminBundle:Team')->findAll();

        return $this->render('DondrekielAdminBundle::admin/team/index.html.twig', array(
            'groups' => $groups,
        ));
    }

    /**
     * Sends a message to a Group.
     *
     * @Route("/send", name="group_message")
     */
    public function sendMessageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentTime = new \DateTime('now');

        $message = new Message();
        $message->setSendTime($currentTime->getTimestamp());
        $message->setGame(null);

        $form = $this->createFormBuilder()
            ->add('teamId', IntegerType::class, array('label' => false, 'required' => true))
            ->add('messageText', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Nachricht senden'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // data is an array with "gameId", "teamId", and "logLevel" keys
            $data = $form->getData();
            $team = $em->getRepository('DondrekielAdminBundle:Team')->find($data['teamId']);
            $message->setTeam($team);
            $message->setMessageText($data['messageText']);

            $em->persist($message);
            $em->flush($message);
        }

        return $this->render('DondrekielAdminBundle::admin/team/message.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * For all undone features.
     *
     * @Route("/todo", name="todo")
     * @Method("GET")
     */
    public function todoAction()
    {
        return $this->render('DondrekielAdminBundle::admin/game/todo.html.twig', array());
    }
}