<?php

namespace DondrekielAdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAdminBundle\Repository\GameRepository;
use DondrekielAppBundle\Entity\Team;

/**
 * ActionLog controller.
 *
 * @Route("admin/logs")
 */
class ActionLogController extends Controller
{
    /**
     * Lists last 100 game action logs.
     *
     * @Route("/", name="actionlog_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('gameId', TextType::class, array('label' => false, 'required' => false))
            ->add('teamId', TextType::class, array('label' => false, 'required' => false))
            ->add('logLevel', ChoiceType::class, array(
                'choices' => array('INFO' => '1', 'WARNING' => '2', 'CRITICAL' => '3'),
                'required' => false
            ))
            ->add('submit', SubmitType::class)
            ->getForm();

        $actionLogs =[];

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            // data is an array with "gameId", "teamId", and "logLevel" keys
            $data = $form->getData();

            if (null == $data['gameId'] && null == $data['teamId'] & null == $data['logLevel']) {
                $actionLogs = $em->getRepository('DondrekielAppBundle:ActionLog')->findAll();
            }

            // check for filters
            if ($data['gameId'] !== null) {
                $filteredLogs = $em->getRepository('DondrekielAppBundle:ActionLog')->findByGame($data['gameId']);
                foreach ($filteredLogs as $filteredLog) {
                    array_push($actionLogs, $filteredLog);
                }
            }

            if ($data['teamId'] !== null) {
                $filteredLogs = $em->getRepository('DondrekielAppBundle:ActionLog')->findByTeam($data['teamId']);
                foreach ($filteredLogs as $filteredLog) {
                    array_push($actionLogs, $filteredLog);
                }
            }

            if ($data['logLevel'] !== null) {
                $filteredLogs = $em->getRepository('DondrekielAppBundle:ActionLog')->findByLogLevel($data['logLevel']);
                foreach ($filteredLogs as $filteredLog) {
                    array_push($actionLogs, $filteredLog);
                }
            }

            // if none was set
            if (null == $data['gameId'] && null == $data['teamId'] & null == $data['logLevel']) {
                return $this->render('DondrekielAdminBundle::admin/actionlog/index.html.twig', array(
                    'logs' => array(array('timestamp' => 'no data', 'game' => 'no data', 'group' => 'no data', 'text' => 'no data', 'loglvl' => 'NONE')),
                    'form' => $form->createView()
                ));
            }
        }

        // if actionLogs still empty (e.g. GET request)
        if (empty($actionLogs)) {
            $actionLogs = $em->getRepository('DondrekielAppBundle:ActionLog')->findAll();
        }

        $logs = [];
        foreach ($actionLogs as $index => $actionLog) {
            $logs[$index]['timestamp'] = date("d.m.Y H:i:s", $actionLog->getTimeStamp());
            $logs[$index]['game'] = $actionLog->getGame()->getName();
            $logs[$index]['group'] = $actionLog->getTeam()->getId();
            $logs[$index]['text'] = $actionLog->getLogText();
            switch ($actionLog->getLogLevel()) {
                case 1: $logs[$index]['loglvl'] = 'INFO';break;
                case 2: $logs[$index]['loglvl'] = 'WARN';break;
                case 3: $logs[$index]['loglvl'] = 'CRITICAL';break;
                default: $logs[$index]['loglvl'] = 'NONE';
            }
        }
        return $this->render('DondrekielAdminBundle::admin/actionlog/index.html.twig', array(
            'logs' => $logs,
            'form' => $form->createView()
        ));
    }
}