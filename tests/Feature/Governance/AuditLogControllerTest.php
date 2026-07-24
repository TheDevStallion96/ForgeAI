<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\Governance\Models\AuditLog;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('redirects guests to login', function () {
    auth()->logout();

    $response = $this->get(route('governance.audit-logs.index'));

    $response->assertRedirect(route('login'));
});

it('lists audit logs', function () {
    AuditLog::factory()->count(5)->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('governance.audit-logs.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('governance/AuditLogs/Index')
        ->has('logs.data', 5),
    );
});

it('paginates audit logs', function () {
    AuditLog::factory()->count(55)->create([
        'organization_id' => $this->user->organization_id,
    ]);

    $response = $this->get(route('governance.audit-logs.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('governance/AuditLogs/Index')
        ->has('logs.data', 50),
    );
});
