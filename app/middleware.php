<?php
declare(strict_types=1);

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
	$app->add(TwigMiddleware::createFromContainer($app, Twig::class));
};
