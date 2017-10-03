<?php

namespace DondrekielBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class StationController extends FOSRestController
{
    public function getStationAction()
    {
        $stationList = [
            "station01" => [
                "id" => 1,
                "name" => "Station 01",
                "ort" => "Haus Siekmann",
                "location" => [
                    "latitude" => 51.8443773,
                    "longitude" => 7.8246521
                ]
            ]
        ];
        return new JsonResponse($stationList);
    }
}