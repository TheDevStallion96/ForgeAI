<?php

namespace App\Domain\AuthTenant\Actions\Fortify;

use App\Domain\AuthTenant\Actions\Teams\CreateTeam;
use App\Domain\AuthTenant\Concerns\PasswordValidationRules;
use App\Domain\AuthTenant\Concerns\ProfileValidationRules;
use App\Domain\AuthTenant\Models\Organization;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(private CreateTeam $createTeam)
    {
        //
    }

    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            $organization = Organization::create([
                'name' => $input['name']."'s Organization",
                'slug' => str($input['name'])->slug()->limit(50).'-'.str()->random(4),
            ]);

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'organization_id' => $organization->id,
            ]);

            $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

            return $user;
        });
    }
}
