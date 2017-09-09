<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\User\UserState;
use Prooph\Common\Messaging\Message;

final class User
{
    const IDENTIFIER = 'userId';

    public static function register(Message $registerUser)
    {
        $event = $registerUser->payload();
        $event['validationCode'] = uniqid();
        yield $event;
    }

    public static function whenUserWasRegistered(Message $userWasRegistered): UserState
    {
        return UserState::fromArray($userWasRegistered->payload());
    }

    public static function changeUsername(UserState $state, Message $changeUsername)
    {
        yield [
            'userId' => $state->userId,
            'oldUsername' => $state->username,
            'newUsername' => $changeUsername->payload()['newUsername']
        ];
    }

    public static function whenUsernameWasChanged(UserState $state, Message $usernameWasChanged): UserState
    {
        $state->username = $usernameWasChanged->payload()['newUsername'];
        return $state;
    }

    public static function validateEmail(UserState $state, Message $validateEmail)
    {
        if($validateEmail->payload()['validationCode'] !== $state->validationCode) {
            throw new \InvalidArgumentException("Wrong validation code");
        }

        yield [
            'userId' => $state->userId
        ];
    }

    public static function whenEmailWasValidated(UserState $state, Message $emailWasValidated): UserState
    {
        $state->validated = true;
        return $state;
    }

    public static function initiateOrganizationCreation(UserState $state, Message $initiateOrganizationCreation)
    {
        if(!$state->validated) {
            throw new \RuntimeException('User email not validated');
        }

        if(in_array($initiateOrganizationCreation->payload()['orgaId'], $state->ownOrganizations)) {
            throw new \RuntimeException("Orga was already created by user");
        }

        yield $initiateOrganizationCreation->payload();
    }

    public static function whenOrganizationCreationWasInitiated(UserState $state, Message $organizationCreationWasInitiated): UserState
    {
        $state->addOrganization($organizationCreationWasInitiated->payload()['orgaId']);
        return $state;
    }

    private function __construct()
    {
        //static usage only
    }
}
