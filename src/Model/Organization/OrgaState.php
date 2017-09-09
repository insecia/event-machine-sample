<?php
declare(strict_types=1);

namespace App\Model\Organization;

final class OrgaState
{
    public $orgaId;

    public $orgaName;

    public $owner;

    public $members = [];

    public $pendingInvitations = [];

    public static function fromArray(array $userProps): self
    {
        $self = new self();

        $self->merge($userProps);

        return $self;
    }

    public function merge(array $props)
    {
        foreach ($props as $prop => $value) {
            if(!property_exists(__CLASS__, $prop)) {
                throw new \InvalidArgumentException(__CLASS__ . " does not have a property $prop");
            }

            $this->{$prop} = $value;
        }
    }

    public function addInvitation(string $memberId)
    {
        $this->pendingInvitations[$memberId] = true;
    }

    public function rmInvitation(string $memberId)
    {
        unset($this->pendingInvitations[$memberId]);
    }
}
