<?php

namespace DondrekielAdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use DondrekielAdminBundle\Game\GameActionLogger;
use DondrekielAdminBundle\Repository\MemberRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Actionlog;

/**
 * @Route("/register", name="register")
 */
class RegistrationController extends BaseController
{
    /**
     * @Route("/", name="get_register")
     * @Method({"GET"})
     */
    public function registerAction(Request $request)
    {
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');
        $errorMessage = false;

        $em = $this->getDoctrine();

        $action = $request->get('action');

        $team = false;
        $memberList = [];

        /* @var MemberRepository $repo */
        $memberRepo = $em->getRepository("DondrekielAdminBundle:Member");
        /* @var TeamRepository $repo */
        $teamRepo = $em->getRepository("DondrekielAdminBundle:Team");

        if ($this->get('session')->has('team')) {
            $teamId = $this->get('session')->get('team');
            $team = $teamRepo->findOneByPasscode($teamId);
        }

        if ($this->get('session')->has('member_list')) {
            $memberIdString = $this->get('session')->get('member_list');
            $memberIdList = explode(',', $memberIdString);
            foreach ($memberIdList as $memberId) {
                /* @var Member $member */
                $member = $memberRepo->findOneByPasscode($memberId);
                if ($member) {
                    $memberList[] = $member;
                } else {
                    $logger->logAction("Registration: Cannot find member with passcode $memberId.", Actionlog::LOGLEVEL_CRIT, $team);
                }
            }
        }

        if ($action == "finish") {
            if (!($team || $this->get('session')->has('team')) || count($memberList) == 0) {
                $logger->logAction("Registration: Cannot finish registration!", Actionlog::LOGLEVEL_CRIT);
            }
            if (!$team) {
                $team = $teamRepo->initializeNewTeam($this->get('session')->get('team'), Team::STATUS_ACTIVE);
            }
            $teamRepo->setTeamMember($team, $memberList);
            $this->get('session')->remove('member_list');
            $this->get('session')->remove('team');
            return new RedirectResponse($this->generateUrl('login'));
        }

        return $this->render('DondrekielAdminBundle::register/index.html.twig',
            ["error_message" => $errorMessage,
                "member_list" => $memberList,
                "team" => $team,
                "team_id" => $this->get('session')->get('team')]
        );
    }

    /**
     * @Route("/cancel", name="cancel_registration")
     * @Method({"GET"})
     */
    public function cancelRegistrationAction(Request $request)
    {
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        $em = $this->getDoctrine();

        $action = $request->get('action');

        $team = false;
        $memberList = [];

        /* @var MemberRepository $repo */
        $memberRepo = $em->getRepository("DondrekielAdminBundle:Member");
        if ($this->get('session')->has('team')) {
            /* @var TeamRepository $repo */
            $teamRepo = $em->getRepository("DondrekielAdminBundle:Team");
            $teamId = $this->get('session')->get('team');
            $team = $teamRepo->findOneByPasscode($teamId);

            $logger->logAction("Registration: Cancel registration for group " . $this->get('session')->has('team') . ".", Actionlog::LOGLEVEL_CRIT, $team);
            $this->get('session')->remove('team');
        }

        if ($this->get('session')->has('member_list')) {
            $memberIdString = $this->get('session')->get('member_list');
            $memberIdList = explode(',', $memberIdString);
            foreach ($memberIdList as $memberId) {
                $logger->logAction("Registration: Cancel registration for member $memberId.", Actionlog::LOGLEVEL_CRIT);
            }
            $this->get('session')->remove('team');
        }

        return new RedirectResponse($this->generateUrl('login'));
    }

    /**
     * @Route("/addPasscode", name="add_passcode_register")
     * @Method({"POST"})
     */
    public function registerAddPasscodeAction(Request $request)
    {
        /* @var GameActionLogger $logger */
        $logger = $this->get('dondrekiel.action.logger');

        $qr = $request->get('qr');

        if (!$qr || !is_string($qr)) {
            $logger->logAction("Failed register action with qr code $qr.", Actionlog::LOGLEVEL_WARN);
            return $this->json(array('error_message' => 'Bitte einen passenden Code einscannen!'), 400);
        }

        try {
            list($codeType, $qrCode) = explode(':', $qr);

        } catch (\Exception $e) {
            $logger->logAction("Failed register action with qr code $qr.", Actionlog::LOGLEVEL_WARN);
            return $this->json(array('error_message' => 'Bitte einen passenden Code einscannen!'), 400);
        }

        if ($qrCode == "") {
            $logger->logAction("Failed register action with qr code $qr.", Actionlog::LOGLEVEL_WARN);
            return $this->json(array('error_message' => 'Bitte einen passenden Code einscannen!'), 400);
        }

        $team = false;
        $memberList = [];

        $em = $this->getDoctrine();

        /* @var MemberRepository $repo */
        $memberRepo = $em->getRepository("DondrekielAdminBundle:Member");
        /* @var TeamRepository $repo */
        $teamRepo = $em->getRepository("DondrekielAdminBundle:Team");

        if ($this->get('session')->has('team')) {
            $teamId = $this->get('session')->get('team');
            $team = $teamRepo->findOneByPasscode($teamId);
        }

        if ($this->get('session')->has('member_list')) {
            $memberIdString = $this->get('session')->get('member_list');
            $memberIdList = explode(',', $memberIdString);
            foreach ($memberIdList as $memberId) {
                /* @var Member $member */
                $member = $memberRepo->findOneByPasscode($memberId);
                if ($member) {
                    $memberList[] = $member;
                } else {
                    $logger->logAction("Registration: Cannot find member with passcode $memberId.", Actionlog::LOGLEVEL_CRIT, $team);
                }
            }
        }

        switch ($codeType) {
            case 'team':
                $teamId = $qrCode;

                if ($team) {
                    return $this->json(array('error_message' => 'Ihr habt schon eine Teamkarte eingescannt!'), 400);
                }
                /* @var TeamRepository $repo */
                $repo = $em->getRepository("DondrekielAdminBundle:Team");
                /* @var Team $team */
                $team = $repo->findOneUnregisteredByPasscode($teamId);
                if ($team) {
                    $this->get('session')->set('team', $teamId);
                    return $this->json(array('success_message' => 'Teamkarte eingescannt!', 'team_id' => $teamId), 200);
                } else {
                    return $this->json(array('error_message' => 'Ihr habt schon eine Teamkarte eingescannt!'), 400);
                }
                break;
            case 'member':
                if ($codeType == 'member') {
                    /* @var TeamRepository $repo */
                    $repo = $em->getRepository("DondrekielAdminBundle:Member");
                    $member = $repo->findOneByPasscode($qrCode);
                    if ($member) {
                        if (!in_array($member, $memberList)) {
                            $memberList[] = $member;
                            $memberIdList[] = $qrCode;
                            $memberIdString = implode(',', array_unique($memberIdList));
                            $this->get('session')->set('member_list', $memberIdString);
                            $enableFinish = false;
                            if (count($memberIdList) >= 3 && $this->get('session')->has('team')) {
                                $enableFinish = true;
                            }
                            return $this->json(array('success_message' => 'Teilnehmerkarte eingescannt!', 'member_id' => $member->getFullName(), 'enable_finish' => $enableFinish), 200);
                        } else {
                            $logger->logAction("Registration: member already registered", Actionlog::LOGLEVEL_WARN, $team);
                        }
                    } else {
                        $logger->logAction("Registration: Cannot find member to add $qrCode.", Actionlog::LOGLEVEL_CRIT, $team);
                    }
                } else {
                    $logger->logAction("Registration: Cannot find member type to add: $codeType.", Actionlog::LOGLEVEL_CRIT, $team);
                }
                return $this->json(array('error_message' => 'Irgendwas ist schief gelaufen!'), 400);
                break;
            case
            'removeMember':
                break;
            default:
                return $this->json(array('error_message' => 'Bitte einen passenden Code einscannen!'), 400);
                break;
        };
        return $this->json(array('error_message' => 'Bitte einen passenden Code einscannen!'), 400);
    }
}