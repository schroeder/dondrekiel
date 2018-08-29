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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Team controller.
 *
 * @Route("admin/team")
 */
class TeamController extends Controller
{
    /**
     * Lists all team entities.
     *
     * @Route("/", name="team_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository('DondrekielAppBundle:Team')->findAll();

        return $this->render('DondrekielAdminBundle::admin/team/index.html.twig', array(
            'teams' => $teams,
        ));
    }

    /**
     * Creates a new team entity.
     *
     * @Route("/new", name="team_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $team = new Team();
        $form = $this->createForm('DondrekielAdminBundle\Form\TeamType', $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush($team);

            return $this->redirectToRoute('team_show', array('id' => $team->getId()));
        }

        return $this->render('DondrekielAdminBundle::admin/team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Sends a message to a Group.
     *
     * @Route("/send", name="team_message")
     */
    public function sendMessageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $message = new Message();
        $message->setCreateTime(time());

        $form = $this->createFormBuilder()
            ->add('receiver', ChoiceType::class, array(
                'choices' => array(
                    'Teams' => Message::TYPE_TEAM,
                    'Stationen' => Message::TYPE_STATION,
                    'Alle' => Message::TYPE_ALL),
                'label' => false,
                'required' => true))
            ->add('messageText', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Nachricht senden'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // data is an array with "gameId", "teamId", and "logLevel" keys
            $data = $form->getData();
            $message->setReceiver($data['receiver']);
            $message->setMessageText($data['messageText']);

            $em->persist($message);
            $em->flush($message);
        }

        return $this->render('DondrekielAdminBundle::admin/team/message.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team entity.
     *
     * @Route("/{id}", name="team_show")
     * @Method("GET")
     */
    public function showAction(Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);

        return $this->render('DondrekielAdminBundle::admin/team/show.html.twig', array(
            'team' => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team entity.
     *
     * @Route("/{id}/edit", name="team_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);
        $editForm = $this->createForm('DondrekielAdminBundle\Form\TeamType', $team);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_index');
        }

        return $this->render('DondrekielAdminBundle::admin/team/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team entity.
     *
     * @Route("/{id}/export", name="team_export")
     * @Method({"GET", "POST"})
     */
    public function exportAction(Request $request, Team $team)
    {
        $html = $this->render('DondrekielAdminBundle::admin/team/export.html.twig', array(
            'team' => $team));

        $input = new StringInput();
        $input->setHtml($html->getContent());


        $converter = new Converter($input, new EmbedOutput());

        //$converter->setOption('n');
        //$converter->setOption('d', '300');

        $converter->setOptions([
            'no-background',
            'margin-bottom' => '100',
            'margin-top' => '100',
        ]);

        $output = $converter->convert();
        $output->embed("game.pdf");
        exit;

    }

    /**
     * Deletes a team entity.
     *
     * @Route("/{id}", name="team_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Team $team)
    {
        $form = $this->createDeleteForm($team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush($team);
        }

        return $this->redirectToRoute('team_index');
    }

    /**
     * Creates a form to delete a team entity.
     *
     * @param Station $team The team entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Team $team)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('team_delete', array('id' => $team->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}