<?php
declare(strict_types = 1);

namespace App\Projection;

use App\Api\MessageDescription;
use App\Infrastructure\MongoDb\MongoConnection;
use MongoDB\Collection;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\AbstractReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require 'config/container.php';

/** @var \Prooph\EventMachine\EventMachine $eventMachine */
$eventMachine = $container->get('eventMachine');

$eventMachine->bootstrap();

/** @var \Prooph\EventStore\Projection\ProjectionManager $projectionManager */
$projectionManager = $container->get('projectionManager');

$readModel = new class($container->get('mongoConnection')) extends AbstractReadModel
{
    const COLLECTION = 'rejected_invitations';
    /**
     * @var MongoConnection
     */
    private $mongoConnection;

    public function __construct(MongoConnection $mongoConnection)
    {
        $this->mongoConnection = $mongoConnection;
    }

    public function init(): void
    {
        //nothing required for mongodb
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        $this->getCollection()->drop();
    }

    protected function add(array $doc): void
    {
        $this->getCollection()->insertOne($doc);
    }

    public function getCollection(): Collection
    {
        return $this->mongoConnection->selectCollection(self::COLLECTION);
    }
};

$projection = $projectionManager->createReadModelProjection(
    'rejected_invitations_projection',
    $readModel,
    [
        ReadModelProjector::OPTION_PERSIST_BLOCK_SIZE => 1
    ]
);

$projection->fromStream('event_stream')
    ->when([
        MessageDescription::EVENT_ORGA_INVITATION_WAS_REJECTED => function(array $state, Message $orgaInvitationWasRejected) {
            /** @var AbstractReadModel $readModel */
            $readModel = $this->readModel();
            $readModel->stack('add', $orgaInvitationWasRejected->payload());
        },
    ])
    ->run(false);

