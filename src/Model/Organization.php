<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\Organization\OrgaState;
use Prooph\Common\Messaging\Message;

final class Organization
{
    const IDENTIFIER = 'orgaId';

    public static function create(Message $createOrga)
    {
        yield $createOrga->payload();
    }

    public static function whenOrgaWasCreated(Message $orgaWasCreated): OrgaState
    {
        return OrgaState::fromArray($orgaWasCreated->payload());
    }

    public static function inviteOrgaMember(OrgaState $state, Message $inviteOrgaMember)
    {
        if($inviteOrgaMember->payload()['invitedBy'] !== $state->owner) {
            throw new \RuntimeException("Member was not invited by the orga owner");
        }

        yield $inviteOrgaMember->payload();
    }

    public static function whenOrgaMemberWasInvited(OrgaState $state, Message $orgaMemberWasInvited): OrgaState
    {
        $state->addInvitation($orgaMemberWasInvited->payload()['memberId']);
        return $state;
    }

    public static function rejectOrgaInvitation(OrgaState $state, Message $rejectOrgaInvitation)
    {
        if(!array_key_exists($rejectOrgaInvitation->payload()['memberId'], $state->pendingInvitations)) {
            throw new \RuntimeException("No pending invitation");
        }

        yield $rejectOrgaInvitation->payload();
    }

    public static function whenOrgaInvitationWasRejected(OrgaState $state, Message $orgaInvitationWasRejected): OrgaState
    {
        $state->rmInvitation($orgaInvitationWasRejected->payload()['memberId']);
        return $state;
    }
}
