<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Responses\ProblemDetailsResponse;
use App\Services\StationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StationController
{
    public function __construct(private StationService $stationService) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $stations = $this->stationService->getAllStations();

        $payload = array_map(
            static fn($station): array => [
                'id' => $station->id,
                'locationName' => $station->locationName,
                'powerKw' => $station->powerKw,
                'isAvailable' => $station->isAvailable,
                '_links' => [
                    'self' => [
                        'href' => '/api/stations/' . $station->id,
                        'method' => 'GET',
                    ],
                ],
            ],
            $stations
        );

        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }

    public function storeSession(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $rawBody = (string) $request->getBody();
            $data = json_decode($rawBody, true);
        }

        if (!is_array($data)) {
            return ProblemDetailsResponse::create(
                $response, 400, 'Requête invalide',
                'Le corps de la requête doit contenir un objet JSON valide.'
            );
        }

        $stationId = filter_var($data['stationId'] ?? null, FILTER_VALIDATE_INT);
        $userName = $data['userName'] ?? null;
        $kwhRequested = filter_var($data['kwhRequested'] ?? null, FILTER_VALIDATE_INT);

        if (
            $stationId === false || $stationId === null || $stationId < 1 ||
            !is_string($userName) || trim($userName) === '' ||
            $kwhRequested === false || $kwhRequested === null || $kwhRequested < 1
        ) {
            return ProblemDetailsResponse::create(
                $response, 400, 'Données manquantes ou invalides',
                'Les champs stationId, userName et kwhRequested sont obligatoires. '
                . 'stationId et kwhRequested doivent être des entiers positifs.'
            );
        }

        $station = $this->stationService->getStationById($stationId);

        if ($station === null) {
            return ProblemDetailsResponse::create(
                $response, 404, 'Station introuvable',
                'Aucune station ne correspond à cet identifiant.'
            );
        }

        if (!$station->isAvailable) {
            return ProblemDetailsResponse::create(
                $response, 409, 'Station indisponible',
                'Cette station ne peut pas accepter une nouvelle session.'
            );
        }

        if ($kwhRequested > $station->powerKw) {
            return ProblemDetailsResponse::create(
                $response, 429, 'Puissance dépassée',
                'La quantité demandée dépasse la limite de puissance de la station.'
            );
        }

        $session = $this->stationService->createChargeSession(
            $stationId,
            trim($userName),
            $kwhRequested
        );

        $payload = [
            'id' => $session->id,
            'stationId' => $session->stationId,
            'userName' => $session->userName,
            'kwhRequested' => $session->kwhRequested,
            'createdAt' => $session->createdAt,
            '_links' => [
                'station' => [
                    'href' => '/api/stations/' . $session->stationId,
                    'method' => 'GET',
                ],
            ],
        ];

        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(201);
    }
}
