<?php

namespace DondrekielAppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use DondrekielAppBundle\Repository\TeamRepository;
use DondrekielAppBundle\Entity\Team;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends FOSRestController
{
    /**
     * @Route("/rest/team", name="rest_get_team")
     */
    public function getTeamAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $doctrine = $this->getDoctrine();
        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("DondrekielAppBundle:Team");

        $teams = $teamRepository->getAllActiveTeams();
        if ($teams && count($teams)) {
            return new JsonResponse(["result" => true, "teams" => $teams]);
        } else {
            return new JsonResponse(["result" => false, "error" => "No data found."]);
        }
    }

    /**
     * @Route("/rest/team/current", name="rest_get_current_team")
     */
    public function getCurrentTeamAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $team = $this->get('security.token_storage')->getToken()->getUser();

        if ($team) {
            $json = $this->get("serializer")->serialize(["result" => true, "current_team" => $team], 'json');
            return new JsonResponse($json, 200, [], true);
            return new JsonResponse(["result" => true, "current_team" => $this->container->get('serializer')
                ->serialize($team, 'array')]);
        } else {
            return new JsonResponse(["result" => false, "error" => "Team not found."]);
        }
    }

    /**
     * @Route("/rest/team/info/{id}", name="rest_get_team_info") requirements = {"id" = "\s+"}
     */
    public function getTeamInfoAction($id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $doctrine = $this->getDoctrine();

        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("DondrekielAppBundle:Team");

        /* @var Team $team */
        $team = $teamRepository->find($id);

        $teamInfo = [
            "id" => $team->getId(),
            "login" => $team->getUsername()
        ];

        $teamInfoHtml = $this->render('DondrekielAppBundle:team:info.html.twig', ['team' => $teamInfo]);
        $teamInfo['content'] = $teamInfoHtml->getContent();

        return new JsonResponse($teamInfo);
    }

}

