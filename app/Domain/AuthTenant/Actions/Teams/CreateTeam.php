<?php

namespace App\Domain\AuthTenant\Actions\Teams;

use App\Domain\AuthTenant\Enums\TeamRole;
use App\Domain\AuthTenant\Models\Team;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTeam
{
    public function handle(User $user, string $name, bool $isPersonal = false): Team
    {
        return DB::transaction(function () use ($user, $name, $isPersonal) {
            $team = Team::create([
                'name' => $name,
                'is_personal' => $isPersonal,
                'organization_id' => $user->organization_id,
            ]);

            $membership = $team->memberships()->create([
                'user_id' => $user->id,
                'role' => TeamRole::Owner,
            ]);

            $user->switchTeam($team);

            return $team;
        });
    }
}
