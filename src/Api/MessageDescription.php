<?php
declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;

final class MessageDescription implements EventMachineDescription
{
    const COMMAND_REGISTER_USER = "App.RegisterUser";
    const COMMAND_CHANGE_USERNAME = "App.ChangeUsername";
    const COMMAND_VALIDATE_EMAIL = 'App.ValidateEmail';
    const COMMAND_INITIATE_ORGA_CREATION = 'App.InitiateOrgaCreation';
    const COMMAND_CREATE_ORGA = "App.CreateOrga";
    const COMMAND_INVITE_ORGA_MEMBER = 'App.InviteOrgaMember';
    const COMMAND_REJECT_ORGA_INVITATION = 'App.RejectOrgaInvitation';

    const EVENT_USER_WAS_REGISTERED = "App.UserWasRegistered";
    const EVENT_USERNAME_WAS_CHANGED = "App.UsernameWasChanged";
    const EVENT_EMAIL_WAS_VALIDATED = 'App.EmailWasValidated';
    const EVENT_ORGA_CREATION_WAS_INITIATED = 'App.OrgaCreationWasInitiated';
    const EVENT_ORGA_WAS_CREATED = 'App.OrgaWasCreated';
    const EVENT_ORGA_MEMBER_WAS_INVITED = 'App.OrgaMemberWasInvited';
    const EVENT_ORGA_INVITATION_WAS_REJECTED = 'App.OrgaInvitationWasRejected';

    public static function describe(EventMachine $eventMachine): void
    {
        $userId = ['type' => 'string', 'pattern' => '^[A-Za-z0-9-]{36}$'];
        $username = ['type' => 'string', 'minLength' => 1];
        $email = ['type' => 'string', 'format' => 'email'];
        $emailValidationCode = ['type' => 'string', 'pattern' => '^[A-Za-z0-9-]{5,}$'];

        $orgaId = ['type' => 'string', 'pattern' => '^[A-Za-z0-9-]{36}$'];
        $orgaName = ['type' => 'string', 'minLength' => 3];

        $eventMachine->registerCommand(self::COMMAND_REGISTER_USER, JsonSchema::object([
            'userId' => $userId,
            'username' => $username,
            'email' => $email
        ]));

        $eventMachine->registerCommand(self::COMMAND_CHANGE_USERNAME, JsonSchema::object([
            'userId' => $userId,
            'newUsername' => $username,
        ]));

        $eventMachine->registerCommand(self::COMMAND_VALIDATE_EMAIL, JsonSchema::object([
            'userId' => $userId,
            'validationCode' => $emailValidationCode,
        ]));

        $eventMachine->registerCommand(self::COMMAND_INITIATE_ORGA_CREATION, JsonSchema::object([
            'userId' => $userId,
            'orgaId' => $orgaId,
            'orgaName' => $orgaName
        ]));

        $eventMachine->registerCommand(self::COMMAND_CREATE_ORGA, JsonSchema::object([
            'owner' => $userId,
            'orgaId' => $orgaId,
            'orgaName' => $orgaName
        ]));

        $eventMachine->registerCommand(self::COMMAND_INVITE_ORGA_MEMBER, JsonSchema::object([
            'invitedBy' => $userId,
            'orgaId' => $orgaId,
            'memberId' => $userId
        ]));

        $eventMachine->registerCommand(self::COMMAND_REJECT_ORGA_INVITATION, JsonSchema::object([
            'memberId' => $userId,
            'orgaId' => $orgaId
        ]));

        //---------- Events ----------------//

        $eventMachine->registerEvent(self::EVENT_USER_WAS_REGISTERED, JsonSchema::object([
            'userId' => $userId,
            'username' => $username,
            'email' => $email,
            'validationCode' => $emailValidationCode,
        ]));

        $eventMachine->registerEvent(self::EVENT_USERNAME_WAS_CHANGED, JsonSchema::object([
            'userId' => $userId,
            'oldUsername' => $username,
            'newUsername' => $username
        ]));

        $eventMachine->registerEvent(self::EVENT_EMAIL_WAS_VALIDATED, JsonSchema::object([
            'userId' => $userId
        ]));

        $eventMachine->registerEvent(self::EVENT_ORGA_CREATION_WAS_INITIATED, JsonSchema::object([
            'userId' => $userId,
            'orgaId' => $orgaId,
            'orgaName' => $orgaName
        ]));

        $eventMachine->registerEvent(self::EVENT_ORGA_WAS_CREATED, JsonSchema::object([
            'owner' => $userId,
            'orgaId' => $orgaId,
            'orgaName' => $orgaName
        ]));

        $eventMachine->registerEvent(self::EVENT_ORGA_MEMBER_WAS_INVITED, JsonSchema::object([
            'invitedBy' => $userId,
            'orgaId' => $orgaId,
            'memberId' => $userId
        ]));

        $eventMachine->registerEvent(self::EVENT_ORGA_INVITATION_WAS_REJECTED, JsonSchema::object([
            'memberId' => $userId,
            'orgaId' => $orgaId
        ]));
    }
}
