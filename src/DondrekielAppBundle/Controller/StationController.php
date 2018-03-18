<?php

namespace DondrekielAppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use DondrekielAppBundle\Repository\StationRepository;
use DondrekielAppBundle\Entity\Station;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends FOSRestController
{
    /**
     * @Route("/rest/station", name="rest_get_station")
     */
    public function getStationAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $doctrine = $this->getDoctrine();

        /* @var StationRepository $stationRepository */
        $stationRepository = $doctrine->getRepository("DondrekielAppBundle:Station");

        $stationObjectList = $stationRepository->findAll();

        $stationList = [];
        /* @var Station $stationObject */
        foreach ($stationObjectList as $stationObject) {
            $stationList[] = [
                "id" => $stationObject->getId(),
                "name" => $stationObject->getName(),
                "identifier" => $stationObject->getIdentifier(),
                "description" => $stationObject->getDescription(),
                "location" => [
                    "latitude" => $stationObject->getLocationLat(),
                    "longitude" => $stationObject->getLocationLng()
                ]
            ];
        }

        return new JsonResponse($stationList);
    }

    /**
     * @Route("/rest/station/info/{id}", name="rest_get_station_info") requirements = {"id" = "\d+"}
     */
    public function getStationInfoAction($id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_TEAM')) {
            return new JsonResponse(["error" => "not allowed"]);
        }

        $doctrine = $this->getDoctrine();

        /* @var StationRepository $stationRepository */
        $stationRepository = $doctrine->getRepository("DondrekielAppBundle:Station");

        /* @var Station $station */
        $station = $stationObjectList = $stationRepository->find($id);

        $stationInfo = [
            "id" => $station->getId(),
            "name" => $station->getName(),
            "identifier" => $station->getIdentifier(),
            "description" => $station->getDescription(),
            "location" => [
                "latitude" => $station->getLocationLat(),
                "longitude" => $station->getLocationLng()
            ]
        ];

        $stationInfoHtml = $this->render('DondrekielAppBundle::/station/info.html.twig', ['station' => $stationInfo]);
        $stationInfo['content'] = $stationInfoHtml;

        return new JsonResponse($stationInfo);
    }

}

