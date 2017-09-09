<?php
declare(strict_types=1);

namespace App\Model\Organization;

use App\Api\MessageDescription;
use App\Model\Organization;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class OrganizationDescription implements EventMachineDescription
{
    const AGGREGATE_ORGA = 'Organization';

    public static function describe(EventMachine $eventMachine): void
    {
        self::describeCreateOrga($eventMachine);
        self::describeInviteOrgaMember($eventMachine);
        self::describeRejectOrgaInvitation($eventMachine);
    }

    private static function describeCreateOrga(EventMachine $eventMachine)
    {
        $eventMachine->process(MessageDescription::COMMAND_CREATE_ORGA)
            ->withNew(self::AGGREGATE_ORGA)
            ->identifiedBy(Organization::IDENTIFIER)
            ->handle([Organization::class, 'create'])
            ->recordThat(MessageDescription::EVENT_ORGA_WAS_CREATED)
            ->apply([Organization::class, 'whenOrgaWasCreated']);
    }

    private static function describeInviteOrgaMember(EventMachine $eventMachine)
    {
        $eventMachine->process(MessageDescription::COMMAND_INVITE_ORGA_MEMBER)
            ->withExisting(self::AGGREGATE_ORGA)
            ->handle([Organization::class, 'inviteOrgaMember'])
            ->recordThat(MessageDescription::EVENT_ORGA_MEMBER_WAS_INVITED)
            ->apply([Organization::class, 'whenOrgaMemberWasInvited']);
    }

    private static function describeRejectOrgaInvitation(EventMachine $eventMachine)
    {
        $eventMachine->process(MessageDescription::COMMAND_REJECT_ORGA_INVITATION)
            ->withExisting(self::AGGREGATE_ORGA)
            ->handle([Organization::class, 'rejectOrgaInvitation'])
            ->recordThat(MessageDescription::EVENT_ORGA_INVITATION_WAS_REJECTED)
            ->apply([Organization::class, 'whenOrgaInvitationWasRejected']);
    }
}
