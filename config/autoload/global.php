<?php
declare(strict_types = 1);

namespace App\Config;

use App\Api\MessageDescription;
use App\Model\Organization\OrganizationDescription;
use App\Model\User\UserDescription;
use App\ProcessManager\CreateOrgaProcessor;

return [
    'environment' => getenv('PROOPH_ENV')?: 'prod',
    'pdo' => [
        'dsn' => getenv('PDO_DSN'),
        'user' => getenv('PDO_USER'),
        'pwd' => getenv('PDO_PWD'),
    ],
    'mongo' => [
        'server' => getenv('MONGO_SERVER'),
        'db' => getenv('MONGO_DB_NAME'),
    ],
    'rabbit' => [
        'connection' => [
            'host' => getenv('RABBIT_HOST')?: 'rabbit',
            'port' => (int)getenv('RABBIT_PORT')?: 5672,
            'login' => getenv('RABBIT_USER')?: 'event-machine',
            'password' => getenv('RABBIT_PWD')?: 'event-machine',
            'vhost' => getenv('RABBIT_VHOST')?: '/event-machine',
            'persistent' => (bool)getenv('RABBIT_PERSISTENT')?: false,
            'read_timeout' => (int)getenv('RABBIT_READ_TIMEOUT')?: 1, //sec, float allowed
            'write_timeout' => (int)getenv('RABBIT_WRITE_TIMEOUT')?: 1, //sec, float allowed,
            'heartbeat' => (int)getenv('RABBIT_HEARTBEAT')?: 0,
            'verify' => false
        ],
        'ui_exchange' => getenv('RABBIT_UI_EXCHANGE')?: 'ui-exchange',
    ],
    'event_machine' => [
        'descriptions' => [
            MessageDescription::class,
            UserDescription::class,
            OrganizationDescription::class,
            CreateOrgaProcessor::class
		]
    ]
];
