<?php
declare(strict_types=1);

namespace App\ProcessManager;

use App\Api\MessageDescription;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class CreateOrgaProcessor implements EventMachineDescription
{
    const DI_SERVICE_ID = 'createOrgaProcessor';

    /**
     * @var EventMachine
     */
    private $eventMachine;

    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->on(MessageDescription::EVENT_ORGA_CREATION_WAS_INITIATED, self::DI_SERVICE_ID);
    }

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    public function __invoke(Message $orgaWasInitiated)
    {
        $payload = $orgaWasInitiated->payload();
        $payload['owner'] = $payload['userId'];
        unset($payload['userId']);

        $this->eventMachine->dispatch(
            MessageDescription::COMMAND_CREATE_ORGA,
            $payload
        );
    }
}
