<?php

namespace DondrekielAdminBundle\Controller;

use DondrekielAppBundle\Entity\GameSubject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DondrekielAdminBundle\Repository\GameSubjectRepository;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\TeamLevel;
use DondrekielAppBundle\Entity\TeamLevelGame;
use DondrekielAdminBundle\Repository\TeamLevelRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAdminBundle\Repository\WordRepository;
use DondrekielAdminBundle\Game\GameLogic;
use DondrekielAdminBundle\Repository\GameRepository;
use DondrekielAdminBundle\Repository\MessageRepository;
use DondrekielAppBundle\Entity\Message;
use DondrekielAppBundle\Entity\Station;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use DondrekielAppBundle\Entity\Actionlog;
use DondrekielAdminBundle\Game\GameActionLogger;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dondrekiel")
     */
    public function indexAction(Request $request)
    {
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $doctrine = $this->getDoctrine();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("DondrekielAdminBundle:Team");

        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $doctrine->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var GameRepository $gameRepository */
        $gameRepository = $doctrine->getRepository("DondrekielAdminBundle:Game");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        $teamCanSetJoker = false;

        /* @var MessageRepository $messageRepository */
        $messageRepository = $doctrine->getRepository("DondrekielAdminBundle:Message");

        /* @var Message $message */
        $message = $messageRepository->findOneByTeam($currentTeam->getId());

        if ($message) {
            return new RedirectResponse($this->generateUrl('show_message'));
        }

        if ($currentTeam->getCurrentLevel() != null) {
            /* @var TeamLevel $currentTeamLevel */
            $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

            if (!$currentTeamLevel) {

                $logger->logAction("Team-Level kann nicht gefunden werden!", Actionlog::LOGLEVEL_GAME_INFO, $currentTeam);

                return $this->render('DondrekielAdminBundle::message/error.html.twig',
                    array('message' => "Irgendwas ist schief gelaufen!"));
            }

            $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();
            $errorMessage = false;

            if ($currentTeam->getCurrentLevel()->getNumber() <= 2 &&
                !$teamRepository->teamAlreadyUsedJoker($currentTeam->getId())
            ) {
                $teamCanSetJoker = true;
            }

            if ($gameSubjectInfoList['count_games_won'] >= 1) {
                $logger->logAction("Team can jump to level " . $currentTeam->getCurrentLevel()->getNumber(), Actionlog::LOGLEVEL_TEAM_INFO, $currentTeam);
                return $this->render('DondrekielAdminBundle::default/jump_level.html.twig',
                    array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam, 'error_message' => $errorMessage));

            }

            if (isset($gameSubjectInfoList['current_game'])) {

                /*if ($gameSubjectInfoList['current_game'] && $gameSubjectInfoList['game_duration'] < 5 * 60) {
                    $logger->logAction("Team cannot set answer yet.", Actionlog::LOGLEVEL_TEAM_WARN, $currentTeam);
                    return $this->render('DondrekielAdminBundle::default/currently_playing.html.twig',
                        array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam));

                } else*/
                if ($gameSubjectInfoList['current_game']) {
                    $logger->logAction("Team can answer game question.", Actionlog::LOGLEVEL_TEAM_INFO, $currentTeam);

                    /* @var WordRepository $wordRepository */
                    $wordRepository = $doctrine->getRepository("DondrekielAdminBundle:Word");

                    $resultOptions[] = $wordRepository->getOneRandom()->getName();
                    $resultOptions[] = $wordRepository->getOneRandom()->getName();
                    $resultOptions[] = $wordRepository->getOneRandom()->getName();
                    $resultOptions[] = $gameSubjectInfoList['current_game']->getGameAnswer();
                    shuffle($resultOptions);
                    $gameSubjectInfoList['result_options'] = $resultOptions;


                    return $this->render('DondrekielAdminBundle::default/enter_result.html.twig',
                        array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam));

                }
            }

        } else {
            return new RedirectResponse($this->generateUrl('login'));
        }

        return $this->render('DondrekielAdminBundle::default/index.html.twig',
            array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam, 'can_set_joker' => $teamCanSetJoker));
    }

    /**
     * @Route("/check_result", name="check_result")
     */
    public function checkResultAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $doctrine = $this->getDoctrine();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("DondrekielAdminBundle:Team");

        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $doctrine->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

        /* @var WordRepository $wordRepository */
        $wordRepository = $doctrine->getRepository("DondrekielAdminBundle:Word");

        $solution = $request->get('solution');

        if (array_key_exists('current_game', $gameSubjectInfoList)) {

            /* @var GameActionLogger $logger */
            $logger = $this->get('dondrekiel.action.logger');

            $message = false;
            if ($solution) {
                if ($solution == $gameSubjectInfoList['current_game']->getGameAnswer()) {
                    $logger->logAction("Team gave correct answer", Actionlog::LOGLEVEL_TEAM_INFO, $currentTeam, $gameSubjectInfoList['current_game']);
                    $em = $doctrine->getEntityManager();
                    /* @var TeamLevelGame $teamLevelGame */
                    $teamLevelGame = $gameSubjectInfoList['current_team_level_game'];
                    $teamLevelGame->setFinishTime(GameLogic::now());
                    $teamLevelGame->setPlayedPoints(GameLogic::getPlayedPoints($currentTeam->getCurrentLevel()->getNumber()));
                    $em->persist($teamLevelGame);
                    $em->flush();

                    /* @var Station $game */
                    $game = $teamLevelGame->getAssignedGame();

                    /* @var GameRepository $gameRepository */
                    $gameRepository = $doctrine->getRepository("DondrekielAdminBundle:Game");

                    $playedRoundsOfGame = $gameRepository->checkPlayedRoundsOfGame($teamLevelGame->getAssignedGame()->getId());

                    if ($playedRoundsOfGame >= $game->getMaxPlayRounds()) {
                        $game->setStatus(Station::STATUS_INACTIVE);
                        $em->persist($game);
                        $em->flush();
                    }

                    return new RedirectResponse($this->generateUrl('dondrekiel'));
                } else {
                    $logger->logAction("Team gave wrong answer", Actionlog::LOGLEVEL_TEAM_WARN, $currentTeam, $gameSubjectInfoList['current_game']);
                    $message = "Die Antwort war leider falsch!";
                }
            }


            $resultOptions[] = $wordRepository->getOneRandom()->getName();
            $resultOptions[] = $wordRepository->getOneRandom()->getName();
            $resultOptions[] = $wordRepository->getOneRandom()->getName();
            $resultOptions[] = $gameSubjectInfoList['current_game']->getGameAnswer();
            shuffle($resultOptions);
            $gameSubjectInfoList['result_options'] = $resultOptions;
            return $this->render('DondrekielAdminBundle::default/enter_result.html.twig',
                array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam, 'message' => $message));
        }
        return new RedirectResponse($this->generateUrl('dondrekiel'));

    }

    /**
     * @Route("/enter_joker", name="enter_joker")
     */
    public
    function enterJokerAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $doctrine = $this->getDoctrine();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("DondrekielAdminBundle:Team");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        if ($currentTeam->getCurrentLevel() &&
            $currentTeam->getCurrentLevel()->getNumber() <= 2 &&
            !$teamRepository->teamAlreadyUsedJoker($currentTeam->getId())
        ) {
            $logger->logAction("Team played joker", Actionlog::LOGLEVEL_TEAM_INFO, $currentTeam);

            return $this->render('DondrekielAdminBundle::joker/index.html.twig',
                array('team' => $currentTeam));


        } else {
            $logger->logAction("Team played invalid joker", Actionlog::LOGLEVEL_TEAM_CRIT, $currentTeam);
            return new RedirectResponse($this->generateUrl('login'));
        }
    }

    /**
     * @Route("/show_message", name="show_message")
     */
    public
    function showMessageAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");
        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

        /* @var MessageRepository $messageRepository */
        $messageRepository = $em->getRepository("DondrekielAdminBundle:Message");

        /* @var Message $message */
        $message = $messageRepository->findOneByTeam($currentTeam->getId());

        if ($message) {

            $message->setReadTime(GameLogic::now());
            $em->persist($message);
            $em->flush();

            return $this->render('DondrekielAdminBundle::message/index.html.twig',
                array('team' => $currentTeam, 'level_info' => $gameSubjectInfoList, 'message' => $message));


        } else {
            return new RedirectResponse($this->generateUrl('dondrekiel'));
        }
    }

    /**
     * @Route("/show_game_message", name="show_game_message")
     */
    public
    function showGameMessageAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_GAME')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var Session $session */
        $session = $request->getSession();
        $gameId = $session->get('game_id');

        $em = $this->getDoctrine()->getEntityManager();

        /* @var MessageRepository $messageRepository */
        $messageRepository = $em->getRepository("DondrekielAdminBundle:Message");

        /* @var Message $message */
        $message = $messageRepository->findOneByGame($gameId);

        if ($message) {

            $message->setReadTime(GameLogic::now());
            $em->persist($message);
            $em->flush();

            return $this->render('DondrekielAdminBundle::message/game.html.twig',
                array('message' => $message));


        } else {
            return new RedirectResponse($this->generateUrl('gameadmin'));
        }
    }
}
