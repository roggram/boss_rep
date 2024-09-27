<?php

declare(strict_types=1);

use App\Application\Actions\AddAction\AddMessageExecAction;
use App\Application\Actions\AddAction\AddSituationAction;
use App\Application\Actions\AddAction\AddSituationExecAction;
use App\Application\Actions\AddAction\AddTriggerAction;
use App\Application\Actions\AddAction\AddTriggerExecAction;
use App\Application\Actions\EditAction\DeleteMessageAction;
use App\Application\Actions\EditAction\EditMessageAction;
use App\Application\Actions\EditAction\EditSituationAction;
use App\Application\Actions\ShowAction\ShowMessageAction;
use App\Application\Actions\ShowAction\ShowSituationAction;
use App\Application\Actions\ShowAction\ShowTriggerAction;
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
        $response->getBody()->write('Hello worldおお＠＠＠!');
        return $response;
    });
    // Trigger
    $app->get('/add_trigger', AddTriggerAction::class);
    $app->post('/add_trigger_exec', AddTriggerExecAction::class);
    $app->get('/show_trigger', ShowTriggerAction::class);
    // Situation
    $app->post('/add_situation_exec', AddSituationExecAction::class);
    $app->get('/add_situation', AddSituationAction::class);
    $app->get('/show_situation', ShowSituationAction::class);
    $app->get('/edit_situation', EditSituationAction::class);
    // Message
    $app->get('/show_message', ShowMessageAction::class);
    $app->post('/add_message', AddMessageExecAction::class);
    $app->get('/edit_situation_message', EditMessageAction::class);
    $app->post('/delete_message', DeleteMessageAction::class);

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
