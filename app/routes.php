<?php

declare(strict_types=1);

use App\Application\Actions\Test\ShowMessageAction;
use App\Application\Actions\Test\ShowSituationAction;
use App\Application\Actions\Test\ShowTriggerAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello worldおお!');
        return $response;
    });
    $app->get('/show_trigger', ShowTriggerAction::class);
    $app->get('/show_situation', ShowSituationAction::class);
    $app->get('/show_message', ShowMessageAction::class);


    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
