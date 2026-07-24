<?php

namespace App\Domain\Agent\Policies;

use App\Domain\Agent\Models\Agent;
use App\Domain\AuthTenant\Models\User;

class AgentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Agent $agent): bool
    {
        return $user->organization_id === $agent->organization_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Agent $agent): bool
    {
        return $user->organization_id === $agent->organization_id;
    }

    public function delete(User $user, Agent $agent): bool
    {
        return $user->organization_id === $agent->organization_id;
    }
}
