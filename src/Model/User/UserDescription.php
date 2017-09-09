<?php
declare(strict_types = 1);

namespace App\Model\User;

use App\Api\MessageDescription;
use App\Model\User;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class UserDescription implements EventMachineDescription
{
    const AGGREGATE_USER = "User";

    public static function describe(EventMachine $eventMachine): void
    {
        self::describeRegisterUser($eventMachine);
        self::describeChangeUsername($eventMachine);
        self::describeValidateEmail($eventMachine);
        self::describeInitiateOrgaCreation($eventMachine);
    }

    private static function describeRegisterUser(EventMachine $eventMachine): void
    {
        $eventMachine->process(MessageDescription::COMMAND_REGISTER_USER)
            ->withNew(self::AGGREGATE_USER)
            ->identifiedBy(User::IDENTIFIER)
            ->handle([User::class, 'register'])
            ->recordThat(MessageDescription::EVENT_USER_WAS_REGISTERED)
            ->apply([User::class, 'whenUserWasRegistered']);

		$eventMachine->on(MessageDescription::EVENT_USER_WAS_REGISTERED, 'uiExchange');
    }

    private static function describeChangeUsername(EventMachine $eventMachine): void
    {
        $eventMachine->process(MessageDescription::COMMAND_CHANGE_USERNAME)
            ->withExisting(self::AGGREGATE_USER)
            ->handle([User::class, 'changeUsername'])
            ->recordThat(MessageDescription::EVENT_USERNAME_WAS_CHANGED)
            ->apply([User::class, 'whenUsernameWasChanged']);
    }

    private static function describeValidateEmail(EventMachine $eventMachine)
    {
        $eventMachine->process(MessageDescription::COMMAND_VALIDATE_EMAIL)
            ->withExisting(self::AGGREGATE_USER)
            ->handle([User::class, 'validateEmail'])
            ->recordThat(MessageDescription::EVENT_EMAIL_WAS_VALIDATED)
            ->apply([User::class, 'whenEmailWasValidated']);
    }

    private static function describeInitiateOrgaCreation(EventMachine $eventMachine)
    {
        $eventMachine->process(MessageDescription::COMMAND_INITIATE_ORGA_CREATION)
            ->withExisting(self::AGGREGATE_USER)
            ->handle([User::class, 'initiateOrganizationCreation'])
            ->recordThat(MessageDescription::EVENT_ORGA_CREATION_WAS_INITIATED)
            ->apply([User::class, 'whenOrganizationCreationWasInitiated']);
    }
}
