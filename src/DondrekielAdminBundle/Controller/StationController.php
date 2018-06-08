<?php

namespace DondrekielAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use DondrekielAppBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spiritix\HtmlToPdf\Converter;
use Spiritix\HtmlToPdf\Input\StringInput;
use Spiritix\HtmlToPdf\Output\EmbedOutput;
use DondrekielAppBundle\Entity\Station;

/**
 * Game controller.
 *
 * @Route("/stations")
 */
class StationController extends Controller
{
    /**
     * Lists all game entities.
     *
     * @Route("/", name="station_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository('DondrekielAppBundle:Station')->findAll();

        return $this->render('DondrekielAdminBundle::admin/station/index.html.twig', array(
            'games' => $games,
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
        return $this->render('DondrekielAdminBundle::admin/station/todo.html.twig', array());
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new", name="station_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $game = new Station();
        $form = $this->createForm('DondrekielAdminBundle\Form\GameType', $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush($game);

            return $this->redirectToRoute('station_show', array('id' => $game->getId()));
        }

        return $this->render('DondrekielAdminBundle::admin/station/new.html.twig', array(
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    /**
     * Sends a message to a Game.
     *
     * @Route("/send", name="station_message")
     */
    public function sendMessageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentTime = new \DateTime('now');

        $message = new Message();
        $message->setSendTime($currentTime->getTimestamp());
        $message->setTeam(null);

        $form = $this->createFormBuilder()
            ->add('gameId', IntegerType::class, array('label' => false, 'required' => true))
            ->add('messageText', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Nachricht senden'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $data = $form->getData();
            $game = $em->getRepository('DondrekielAppBundle:Game')->find($data['gameId']);
            $message->setGame($game);
            $message->setMessageText($data['messageText']);

            $em->persist($message);
            $em->flush($message);
        }

        return $this->render('DondrekielAdminBundle::admin/station/message.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a game entity.
     *
     * @Route("/{id}", name="station_show")
     * @Method("GET")
     */
    public function showAction(Station $game)
    {
        $deleteForm = $this->createDeleteForm($game);

        return $this->render('DondrekielAdminBundle::admin/station/show.html.twig', array(
            'game' => $game,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/edit", name="station_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Station $game)
    {
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('DondrekielAdminBundle\Form\GameType', $game);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('station_edit', array('id' => $game->getId()));
        }

        return $this->render('DondrekielAdminBundle::admin/station/edit.html.twig', array(
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/export", name="station_export")
     * @Method({"GET", "POST"})
     */
    public function exportAction(Request $request, Station $game)
    {
        $html = $this->render('DondrekielAdminBundle::admin/station/export.html.twig', array(
            'game' => $game));

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
     * Deletes a game entity.
     *
     * @Route("/{id}", name="station_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Station $game)
    {
        $form = $this->createDeleteForm($game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($game);
            $em->flush($game);
        }

        return $this->redirectToRoute('station_index');
    }

    /**
     * Creates a form to delete a game entity.
     *
     * @param Station $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Station $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('station_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
