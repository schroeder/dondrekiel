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
        /*
         * SELECT p1.*, t.* FROM team t, position p1 WHERE t.id=p1.team_id AND p1.timestamp= (SELECT MAX(p2.timestamp) FROM position p2 WHERE p2.team_id=p1.team_id);
         * */
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

