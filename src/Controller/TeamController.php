<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\ViewHandlerInterface;

//use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use DondrekielAppBundle\Repository\TeamRepository;
use App\Entity\Team;
use App\Entity\Position;
use Doctrine\Persistence\ManagerRegistry;


class TeamController extends AbstractFOSRestController
{
    #[Route('/rest/team', name: 'rest_get_team')]
    public function getTeamAction(ManagerRegistry $doctrine, Request $request,SerializerInterface $serializer): JsonResponse
    {
        if (false === ($this->isGranted('ROLE_TEAM') ||
                $this->isGranted('ROLE_ADMIN'))) {
            return new JsonResponse(["error" => "not allowed"]);
        }
        
        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository(Team::class);

        $teams = $teamRepository->getAllActiveTeams();

        if ($teams && count($teams)) {
            $data = [];
            foreach($teams as $team){
                $data[] = $serializer->normalize($team);
            }
            $result = ["result" => true, "teams" => $data];
            return new JsonResponse($result, 200);
        } else {
            return new JsonResponse(["result" => false, "error" => "No data found."]);
        }
    }

    #[Route('/rest/team/current', name: 'rest_get_current_team')]
    public function getCurrentTeamAction(ManagerRegistry $doctrine, Request $request,SerializerInterface $serializer): JsonResponse
    {
        if (false === $this->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $teamRepository = $doctrine->getRepository(Team::class);
        $team = $this->getUser();

        if ($team){
            $result = ["result" => true, "current_team" => $serializer->normalize($team)];
        }
        else{
            $result = ["result" => false];

        }


        return new JsonResponse($result, 200);

    }

    #[Route('/rest/team/position', name: 'current_team_position', methods: ['POST'])]
    public function setCurrentTeamPosition(
        Request $request,
        ManagerRegistry $doctrine
    ): Response {

        if (false === $this->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not  to set position"]);
        }

        $team = $this->getUser();

        $entityManager = $doctrine->getManager();



        $teamId = $request->getPayload()->get('team', false);

        if ($team->getId() != $teamId) {
            return new JsonResponse(["error" => "not a valid team"]);
        }

        $locationLat = $request->getPayload()->get('latitude', false);
        $locationLng = $request->getPayload()->get('longitude', false);


        $position = new Position();
        $position->setLocationLat($locationLat);
        $position->setLocationLng($locationLng);
        $position->setTeam($team);
        $position->setTimestamp(time());

        $entityManager->persist($position);

        $team->setLocationLat($locationLat);
        $team->setLocationLng($locationLng);

        $entityManager->persist($team);
        $entityManager->flush();



        $result = [
            $teamId,
            $locationLat,
            $locationLng
        ];

        return new JsonResponse($result, 200);


    }



    #[Route('/rest/team/info/{id}', name: 'rest_get_team_info')]
    public function getTeamInfoAction(
        $id,
        ManagerRegistry $doctrine
        )
    {
        if (false === $this->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        /* @var TeamRepository $teamRepository */
        $teamRepository = $doctrine->getRepository("Team");

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
