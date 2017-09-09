<?php
declare(strict_types=1);

namespace App\Model\User;

use Ramsey\Uuid\UuidInterface;

final class UserState
{
    public $userId;

    public $email;

    public $username;

    public $validated = false;

    public $validationCode;

    public $ownOrganizations = [];

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

    public function addOrganization(string $orgaId)
    {
        $this->ownOrganizations[] = $orgaId;
    }
}
