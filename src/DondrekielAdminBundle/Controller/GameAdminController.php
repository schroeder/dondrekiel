<?php

namespace DondrekielAdminBundle\Controller;

use DondrekielAppBundle\Entity\GameSubject;
use DondrekielAppBundle\Entity\TeamLevelGame;
use DondrekielAdminBundle\Game\GameLogic;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DondrekielAdminBundle\Repository\GameSubjectRepository;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\TeamLevel;
use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Member;
use DondrekielAdminBundle\Repository\TeamLevelRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAdminBundle\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use DondrekielAppBundle\Entity\Actionlog;
use DondrekielAdminBundle\Game\GameActionLogger;
use DondrekielAppBundle\Entity\Message;
use DondrekielAdminBundle\Repository\MessageRepository;

class GameAdminController extends Controller
{
    /**
     * @Route("/gameadmin", name="gameadmin")
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_GAME')) {
            return new RedirectResponse($this->generateUrl('login'));
        }

        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        /* @var Session $session */
        $session = $request->getSession();
        $gameId = $session->get('game_id');
        $gamePasscode = $session->get('game_passcode');

        $em = $this->getDoctrine();

        /* @var MessageRepository $messageRepository */
        $messageRepository = $em->getRepository("DondrekielAdminBundle:Message");

        /* @var Message $message */
        $message = $messageRepository->findOneByGame($gameId);

        if ($message) {
            return new RedirectResponse($this->generateUrl('show_game_message'));
        }

        /* @var GameRepository $gameRepo */
        $gameRepo = $em->getRepository("DondrekielAdminBundle:Game");

        /* @var Station $game */
        $game = $gameRepo->findGameByPasscode($gamePasscode);

        $teams = $gameRepo->getCurrentTeams($gameId);

        $logger->logAction("Show game page.", Actionlog::LOGLEVEL_GAME_INFO, null, $game);

        return $this->render('DondrekielAdminBundle::gameadmin/index.html.twig',
            array('error_message' => '', 'game' => $game, 'teams' => $teams));
    }

    /**
     * @Route("/gameadmin/check", name="gameadmincheck")
     */
    public function gameCheckAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_GAME')) {
            return new RedirectResponse($this->generateUrl('login'));
        }
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        $em = $this->getDoctrine()->getEntityManager();

        /* @var Session $session */
        $session = $request->getSession();
        $gamePasscode = $session->get('game_passcode');

        /* @var GameRepository $gameRepo */
        $gameRepo = $em->getRepository("DondrekielAdminBundle:Game");

        /* @var Station $game */
        $game = $gameRepo->findGameByPasscode($gamePasscode);

        $scannedQRCode = $request->get('qr');

        $memberRepository = $em->getRepository("DondrekielAdminBundle:Member");
        /* @var Member $member */
        $member = $memberRepository->findOneByPasscode($scannedQRCode);
        if (!$member) {
            $logger->logAction("Wrong team at game.", Actionlog::LOGLEVEL_GAME_WARN, null, $game);
            $errorMessage = "Leider konnte ich das Team nicht finden!";
            return $this->render('DondrekielAdminBundle::gameadmin/error.html.twig',
                array('error_message' => '', 'game' => $game, 'error_message' => $errorMessage));
        }

        /* @var Team $team */
        $team = $member->getTeam();

        /*        if ($team && $team->getParentTeam() != null) {
                    $repo = $em->getRepository("DondrekielAdminBundle:Team");
                    $team = $repo->findLeadingGroup($team->getPasscode());
                }*/

        if (!$team) {
            $logger->logAction("Cannot find team.", Actionlog::LOGLEVEL_GAME_CRIT, null, $game);
            $errorMessage = "Leider konnte ich das Team nicht finden!";
            return $this->render('DondrekielAdminBundle::gameadmin/error.html.twig',
                array('error_message' => '', 'game' => $game, 'error_message' => $errorMessage));
        }

        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");
        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($team, $team->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

        /* @var Station $teamGame */
        $teamGame = false;
        if (array_key_exists('current_game', $gameSubjectInfoList)) {
            $teamGame = $gameSubjectInfoList['current_game'];
        }

        if (!$teamGame) {
            $logger->logAction("Team tried to play wrong game ", Actionlog::LOGLEVEL_GAME_WARN, $team, $game);

            $errorMessage = "Das Team muss sich ein neuen Spiel an einem Terminal abholen!\n";
            return $this->render('DondrekielAdminBundle::gameadmin/error.html.twig',
                array('error_message' => '', 'game' => $game, 'error_message' => $errorMessage));
        }

        if ($teamGame->getId() != $game->getId()) {
            $logger->logAction("Team tried to play wrong game ", Actionlog::LOGLEVEL_GAME_WARN, $team, $game);

            $errorMessage = "Das Team soll dieses Spiel nicht spielen!\n";
            $errorMessage .= "Schick es bitte zu \"" . $teamGame->getName() . "\" (" . $teamGame->getIdentifier() . ")";
            $errorMessage .= " Ort: " . $teamGame->getLocation() . "";
            return $this->render('DondrekielAdminBundle::gameadmin/error.html.twig',
                array('error_message' => '', 'game' => $game, 'error_message' => $errorMessage));
        }

        $session->set('team_id', $team->getId());

        /* @var TeamLevelGame $teamLevelGame */
        $teamLevelGame = $gameSubjectInfoList['current_team_level_game'];

        $teamLevelGame->setStartTime(GameLogic::now());
        $em->persist($teamLevelGame);
        $em->flush();

        $logger->logAction("Team started game", Actionlog::LOGLEVEL_GAME_INFO, $team, $game);

        return $this->render('DondrekielAdminBundle::gameadmin/play.html.twig',
            array('error_message' => '', 'game' => $game));
    }

    /**
     * @Route("/gameadmin/won", name="gameadminwon")
     */
    public function gameWonAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_GAME')) {
            return new RedirectResponse($this->generateUrl('login'));
        }
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        $em = $this->getDoctrine()->getEntityManager();

        /* @var Session $session */
        $session = $request->getSession();
        $gamePasscode = $session->get('game_passcode');
        $teamId = $session->get('team_id');

        if (!$teamId || !$gamePasscode) {
            return new RedirectResponse($this->generateUrl('gameadmin'));
        }

        /* @var GameRepository $gameRepo */
        $gameRepo = $em->getRepository("DondrekielAdminBundle:Game");

        /* @var Station $game */
        $game = $gameRepo->findGameByPasscode($gamePasscode);


        /* @var TeamRepository $repo */
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");

        /* @var Team $team */
        $team = $teamRepository->find($teamId);

        /*if ($team && $team->getParentTeam() != null) {
            $team = $repo->findLeadingGroup($team->getPasscode());
        }*/

        if (!$team) {
            $errorMessage = "Leider konnte ich das Team nicht finden!";
            return $this->render('DondrekielAdminBundle::gameadmin/error.html.twig',
                array('error_message' => '', 'game' => $game, 'error_message' => $errorMessage));
        }

        $teamLevelRepository = $em->getRepository("DondrekielAdminBundle:TeamLevel");
        /* @var TeamLevel $currentTeamLevel */
        $currentTeamLevel = $teamLevelRepository->getCurrentTeamLevel($team, $team->getCurrentLevel());

        $gameSubjectInfoList = $currentTeamLevel->getTeamLevelInfo();

        /* @var TeamLevelGame $teamLevelGame */
        $teamLevelGame = $gameSubjectInfoList['current_team_level_game'];

        $teamLevelGame->setFinishTime(GameLogic::now());
        $teamLevelGame->setPlayedPoints(GameLogic::getPlayedPoints($currentTeamLevel->getLevel()->getNumber()));
        $em->persist($teamLevelGame);
        $em->flush();
        $session->remove('team_id');
        $logger->logAction("Team finished game", Actionlog::LOGLEVEL_GAME_INFO, $team, $game);

        return $this->render('DondrekielAdminBundle::gameadmin/success.html.twig',
            array('success_message' => 'Das Spiel wurde erfolgreich bestanden!', 'game' => $game));
    }
}
