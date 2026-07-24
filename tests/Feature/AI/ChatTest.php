<?php

use App\Domain\AuthTenant\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Ai\AnonymousAgent;

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

it('can post a prompt and receive a streamed response', function () {
    AnonymousAgent::fake(['AI response here.']);

    $response = $this
        ->actingAs($this->user)
        ->post(route('ai.chat'), [
            'prompt' => 'Hello AI!',
        ]);

    $response->assertOk();
    expect($response->getStatusCode())->toBe(200);
});

it('validates the prompt is required', function () {
    $response = $this
        ->actingAs($this->user)
        ->post(route('ai.chat'), [
            'prompt' => '',
        ]);

    $response->assertInvalid(['prompt']);
});

it('validates the prompt does not exceed 2000 characters', function () {
    $response = $this
        ->actingAs($this->user)
        ->post(route('ai.chat'), [
            'prompt' => str_repeat('a', 2001),
        ]);

    $response->assertInvalid(['prompt']);
});
