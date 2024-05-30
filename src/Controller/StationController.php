<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use DondrekielAppBundle\Repository\TeamRepository;
use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;

class StationController extends AbstractController
{
    #[Route('/rest/station', name: 'rest_get_station')]
    public function getStationAction(ManagerRegistry $doctrine, Request $request,SerializerInterface $serializer): JsonResponse
    {
        /*if ((false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) &&
            (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {
            return new JsonResponse(["error" => "not allowed"]);
        }*/

        $teamRepository = $doctrine->getRepository(Station::class);

        $stationObjectList = $teamRepository->findAll();

        $stationList = [];
        foreach ($stationObjectList as $stationObject) {
            $stationList[] = [
                "id" => $stationObject->getId(),
                "name" => $stationObject->getName(),
                "identifier" => $stationObject->getIdentifier(),
                "status" => $stationObject->getStatus(),
                "organizer" => $stationObject->getOrganizer(),
                "description" => $stationObject->getDescription(),
                "location" => [
                    "latitude" => $stationObject->getLocationLat(),
                    "longitude" => $stationObject->getLocationLng()
                ]
            ];
        }

        return new JsonResponse($stationList, 200);

    }
}
