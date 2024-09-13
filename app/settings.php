<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                // Twig設定
                'twig' => [
                    'reloadCache'      => time()
                   ,'strict_variables' => true
                   ,'cache'            => __DIR__ . '/../var/cache/twig'
               ]
               // CSS,JS,画像置き場
               ,'assets' =>[
                   'path'              => '/assets' 
               ],
               'db' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'boss_rep_db',
                    'username' => 'michizoeakito',
                    'password' => 'asahizoe15',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
                ]
            ]);
        }
    ]);
};
