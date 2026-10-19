<?php

declare(strict_types=1);

namespace App\Responses;

use Psr\Http\Message\ResponseInterface;

class ProblemDetailsResponse
{
    public static function create(
        ResponseInterface $response,
        int $status,
        string $title,
        string $detail,
        string $type = 'about:blank'
    ): ResponseInterface {
        $payload = [
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
        ];

        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/problem+json; charset=utf-8')
            ->withStatus($status);
    }
}
