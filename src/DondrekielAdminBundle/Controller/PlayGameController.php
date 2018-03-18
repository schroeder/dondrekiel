<?php

namespace DondrekielAdminBundle\Controller;

use DondrekielAppBundle\Entity\TeamLevel;
use DondrekielAppBundle\Entity\TeamLevelGame;
use DondrekielAdminBundle\Game\GameLogic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DondrekielAdminBundle\Repository\GameSubjectRepository;
use DondrekielAdminBundle\Repository\GameRepository;
use DondrekielAppBundle\Entity\Team;
use DondrekielAdminBundle\Repository\TeamLevelRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use DondrekielAdminBundle\Repository\MemberRepository;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Joker;
use DondrekielAdminBundle\Game\GameActionLogger;
use DondrekielAppBundle\Entity\Actionlog;


class PlayGameController extends Controller
{
    /**
     * @Route("/play/selectgame", name="playselectgame")
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        $selectedCategory = $request->get('subject');

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        /* @var GameSubjectRepository $gameSubjectRepository */
        $gameSubjectRepository = $em->getRepository("DondrekielAdminBundle:GameSubject");

        /* @var GameRepository $gameRepository */
        $gameRepository = $em->getRepository("DondrekielAdminBundle:Game");

        /* @var TeamRepository $teamRepository */
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");

        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

        if (!$currentTeamLevel->getTeamLevelGames()) {
        }

        $currentGame = $gameRepository->getCurrentGame($currentTeam);


        $gameSubjectList = [];
        /* @var TeamLevelGame $teamLevelGame */
        foreach ($currentTeamLevel->getTeamLevelGames() as $teamLevelGame) {
            $gameSubject = $teamLevelGame->getAssignedGameSubject();
            $game = false;
            if ($gameSubject->getId() == $selectedCategory) {
                $game = $teamLevelGame->getAssignedGame();
                $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

                if ($game && $teamLevelGame->getStartTime() && !$teamLevelGame->getFinishTime()) {
                    $logger->logAction("Team cannot choose game. Already playing", Actionlog::LOGLEVEL_GAME_CRIT, $currentTeam, $game);
                    return new RedirectResponse($this->generateUrl('dondrekiel'));
                } elseif (!$game) {
                    $logger->logAction("Team started game.", Actionlog::LOGLEVEL_GAME_INFO, $currentTeam, $game);
                    // select a game
                    $currentGame = $gameRepository->findAFreeGame($currentTeamLevel);
                    $teamLevelGame->setAssignedGame($currentGame);
                    $teamLevelGame->setStartTime(GameLogic::now());
                    $em->persist($teamLevelGame);
                    $em->flush();

                    $em->refresh($currentGame);
                    return $this->render('DondrekielAdminBundle::play/show_game.html.twig',
                        array('team_level' => $teamLevelGame, 'game' => $currentGame, 'team' => $currentTeam, 'level_info' => $gameSubjectInfoList));
                } else {
                    $logger->logAction("Team cannot choose game. Already playing", Actionlog::LOGLEVEL_GAME_WARN, $currentTeam, $game);
                    return $this->render('DondrekielAdminBundle::play/show_game.html.twig',
                        array('team_level' => $teamLevelGame, 'game' => $game, 'team' => $currentTeam, 'level_info' => $gameSubjectInfoList));
                }
            }
        }
    }

    /**
     * @Route("/play/jumplevel", name="playjumplevel")
     */
    public function jumpLevelAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getEntityManager();

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");

        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();
        $errorMessage = false;
        $levelJumpTeam = false;

        if ($gameSubjectInfoList['count_games_won'] < 1) {
            $errorMessage = "Ihr könnt noch kein Level aufsteigen!";
        } else {
            $jumpLevelTeamPasscode = $request->get('qr');

            if ($jumpLevelTeamPasscode) {
                /* @var MemberRepository $repo */
                $repo = $em->getRepository("DondrekielAdminBundle:Member");
                /* @var Member $member */
                $member = $repo->findOneByPasscode($jumpLevelTeamPasscode);

                if ($member) {
                    /* @var Team $team */
                    $levelJumpTeam = $member->getTeam();
                }
            }

        }

        /* @var TeamLevel $currentTeamLevel */
        $levelJumpTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());
        $gameSubjectInfoList = $levelJumpTeamLevel->getTeamLevelInfo();

        if ($gameSubjectInfoList['count_games_won'] < 1) {
            $errorMessage = "Ihr könnt noch kein Level aufsteigen, ihr habt noch keine zwei Spiele gewonnen!";
        } elseif ($levelJumpTeam && !$errorMessage) {
            if ($levelJumpTeam->getId() == $currentTeam->getId()) {
                $errorMessage = "Ihr müsst euch eine andere Gruppe suchen!";
            } elseif ($levelJumpTeam->getCurrentLevel() != $currentTeam->getCurrentLevel()) {
                $errorMessage = "Ihr müsst euch eine Gruppe aus Level " . $currentTeam->getCurrentLevel() . " suchen!";

            }/* elseif ($levelJumpTeam->getGrade() != $currentTeam->getGrade()) {
                $errorMessage = "Ihr müsst euch eine Gruppe aus eurer Stufe suchen!";

            }*/ else { /* Gruppen zusammenführen und zum Spielfeld*/

                $currentTeamLevel->setFinishTime(GameLogic::now());
                $em->persist($currentTeamLevel);

                $levelJumpTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());
                $levelJumpTeamLevel->setFinishTime(GameLogic::now());
                $em->persist($currentTeamLevel);

                $newLevelNumber = $currentTeam->getCurrentLevel()->getNumber() + 1;

                if ($newLevelNumber == 7) {
                    return new RedirectResponse($this->generateUrl('playgamefinished'));
                }

                $levelRepository = $em->getRepository("DondrekielAdminBundle:Level");
                $newLevel = $levelRepository->findOneByNumber($newLevelNumber);

                //$levelJumpTeam->setParentTeam($currentTeam);
                //$currentTeam->setParentTeam($currentTeam);
                $currentTeam->setCurrentLevel($newLevel);
                $levelJumpTeam->setCurrentLevel($newLevel);
                $em->persist($currentTeam);
                $em->persist($levelJumpTeam);

                /* @var Member $teamMember */
                foreach ($levelJumpTeam->getTeamMembers() as $teamMember) {
                    $teamMember->setTeam($currentTeam);
                    $em->persist($teamMember);
                }

                $newTeamLevel = new TeamLevel();

                $newTeamLevel->setTeam($currentTeam);
                $newTeamLevel->setStartTime(GameLogic::now());
                $newTeamLevel->setLevel($newLevel);

                $em->persist($newTeamLevel);
                $em->flush();

                /* @var GameSubjectRepository $repository */
                $repository = $em->getRepository("DondrekielAdminBundle:GameSubject");
                $gameSubjectList = $repository->getFourRandomGameSubjects();

                foreach ($gameSubjectList as $gameSubject) {
                    $teamLevelGame = new TeamLevelGame();
                    $teamLevelGame->setAssignedGameSubject($gameSubject);
                    $teamLevelGame->setStartTime(GameLogic::now());
                    $teamLevelGame->setTeamLevel($newTeamLevel);
                    $em->persist($teamLevelGame);
                    $em->flush();
                }
                $em->flush();

                return new RedirectResponse($this->generateUrl('dondrekiel'));
            }

        }

        return $this->render('DondrekielAdminBundle::default/jump_level.html.twig',
            array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam, 'error_message' => $errorMessage));
    }

    /**
     * @Route("/play/joker", name="playjoker")
     */
    public function playJokerAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        $errorMessage = false;

        $jokerId = $request->get('qr');
        $jokerInfo = (explode(":", $jokerId));

        /* @var Team $currentTeam */
        $currentTeam = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        /* @var GameSubjectRepository $gameSubjectRepository */
        $gameSubjectRepository = $em->getRepository("DondrekielAdminBundle:GameSubject");

        /* @var GameRepository $gameRepository */
        $gameRepository = $em->getRepository("DondrekielAdminBundle:Game");

        /* @var TeamRepository $teamRepository */
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");

        /* @var TeamLevelRepository $teamLevelRepository */
        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");

        /* @var Team $currentTeam */
        $currentTeam = $teamRepository->find($currentTeam->getId());

        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($currentTeam, $currentTeam->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

        /* @var JokerRepository $jokerRepository */
        $jokerRepository = $em->getRepository("DondrekielAdminBundle:Joker");

        /* @var Joker $joker */
        $joker = $jokerRepository->findOneByJokercode($jokerId);

        if ($jokerInfo[0] == 'joker') {
            $jokerId = $jokerInfo[1];
        } else {
        }

        if (!$joker || $joker->getJokerUsed() == true) {
            $errorMessage = "Schade, leider wurde der Joker schon eingesetzt!";
        }

        if ($teamRepository->teamAlreadyUsedJoker($currentTeam->getId())
        ) {
            $errorMessage = "Schade, leider habt ihr schon einen Joker gespielt!";
        }
        if ($currentTeam->getCurrentLevel()->getNumber() > 2) {
            $errorMessage = "Schade, leider könnt ihr die Joker nur in den ersten zwei Level spielen!";
        }

        if ($errorMessage != false) {
            return $this->render('DondrekielAdminBundle::joker/message.html.twig',
                array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam, 'error_message' => $errorMessage));
        }

        $gameSubjectList = [];
        /* @var TeamLevelGame $teamLevelGame */
        foreach ($currentTeamLevel->getTeamLevelGames() as $teamLevelGame) {
            $gameSubject = $teamLevelGame->getAssignedGameSubject();
            $game = false;
            if ($teamLevelGame->getAssignedGame() == null) {


                $teamLevelGame->setFinishTime(GameLogic::now());
                $teamLevelGame->setPlayedPoints(GameLogic::getPlayedPoints());
                $teamLevelGame->setUsedJoker($joker);
                $em->persist($teamLevelGame);

                $joker->setJokerUsed(true);
                $em->persist($joker);
                $em->flush();

                return $this->render('DondrekielAdminBundle::joker/success.html.twig',
                    array('level_info' => $gameSubjectInfoList, 'team' => $currentTeam));

            }
        }
    }

    /**
     * @Route("/play/finished", name="playgamefinished")
     */
    public function gameFinishedAction(Request $request)
    {
        return $this->render('DondrekielAdminBundle::play/success.html.twig', array());
        /*
         * TODO: Erfolgsnachricht, Feuerwerk!
         *
         * */
    }
}
