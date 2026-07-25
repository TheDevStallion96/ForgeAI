<?php

use App\Domain\AuthTenant\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('redirects guests to login', function () {
    $response = $this->get(route('ai.chat'));

    $response->assertRedirect(route('login'));
});

it('renders the chat page for authenticated users', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('ai.chat'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('AI/Chat'),
    );
});

it('validates the prompt is required', function () {
    $response = $this
        ->actingAs($this->user)
        ->post(route('ai.chat'), [
            'prompt' => '',
        ]);

    $response->assertInvalid(['prompt']);
});

it('validates the prompt does not exceed 4000 characters', function () {
    $response = $this
        ->actingAs($this->user)
        ->post(route('ai.chat'), [
            'prompt' => str_repeat('a', 4001),
        ]);

    $response->assertInvalid(['prompt']);
});
