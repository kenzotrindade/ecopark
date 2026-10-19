<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EcoMetricsMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $start = microtime(true);
        $response = $handler->handle($request);

        $executionTimeMs = (microtime(true) - $start) * 1000;
        $payloadSizeBytes = $response->getBody()->getSize() ?? 0;

        $estimatedCo2Grams = ($payloadSizeBytes * 0.000000011)
            + ($executionTimeMs * 0.000001);

        return $response
            ->withHeader('X-Response-Time-Ms', number_format($executionTimeMs, 3, '.', ''))
            ->withHeader('X-Payload-Size-Bytes', (string) $payloadSizeBytes)
            ->withHeader('X-Estimated-CO2-Grams', number_format($estimatedCo2Grams, 9, '.', ''));
    }
}
