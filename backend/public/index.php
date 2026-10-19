<?php

declare(strict_types=1);

use App\Controllers\StationController;
use App\Core\Database;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\EcoMetricsMiddleware;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions([
    PDO::class => static fn() => Database::getInstance(),
]);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$app->get('/api/stations', [StationController::class, 'index']);
$app->post('/api/sessions', [StationController::class, 'storeSession']);

$app->options('/{routes:.*}', static function ($request, $response) {
    return $response->withStatus(204);
});

$app->add(new CorsMiddleware());
$app->add(new EcoMetricsMiddleware());

$app->addErrorMiddleware(true, true, true);

$app->run();
