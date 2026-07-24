<?php

namespace App\Domain\AuthTenant\Rules;

use App\Domain\AuthTenant\Models\TeamInvitation;
use App\Domain\AuthTenant\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTeamInvitation implements ValidationRule
{
    public function __construct(protected ?User $user)
    {
        //
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof TeamInvitation || ! $this->user instanceof User) {
            $fail(__('This invitation was sent to a different email address.'));

            return;
        }

        if ($value->isAccepted()) {
            $fail(__('This invitation has already been accepted.'));

            return;
        }

        if ($value->isExpired()) {
            $fail(__('This invitation has expired.'));

            return;
        }

        if (strtolower($value->email) !== strtolower($this->user->email)) {
            $fail(__('This invitation was sent to a different email address.'));
        }
    }
}
