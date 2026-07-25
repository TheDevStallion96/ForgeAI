<?php

use App\Domain\AuthTenant\Models\User;
use App\Domain\KnowledgeVector\Models\KnowledgeAsset;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Files;
use Laravel\Ai\Stores;

function makeUploadFile(string $name, string $mimeType): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'kbtest_');
    file_put_contents($path, 'test document content for '.$name);

    return new UploadedFile($path, $name, $mimeType, null, true);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    Stores::fake();
    Files::fake();
});

it('redirects guests to login', function () {
    $response = $this->get(route('knowledge.index'));

    $response->assertRedirect(route('login'));
});

it('renders the knowledge hub page', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('knowledge.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledge/Index')
        ->has('assets'),
    );
});

it('displays uploaded assets', function () {
    KnowledgeAsset::factory()->create([
        'organization_id' => $this->user->organization_id,
        'name' => 'test.pdf',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->get(route('knowledge.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('knowledge/Index')
        ->has('assets', 1)
        ->where('assets.0.name', 'test.pdf'),
    );
});

it('can upload a file', function () {
    $file = makeUploadFile('report.pdf', 'application/pdf');

    $response = $this
        ->actingAs($this->user)
        ->post(route('knowledge.store'), ['file' => $file]);

    $response->assertRedirect(route('knowledge.index'));

    $this->assertDatabaseHas('knowledge_assets', [
        'organization_id' => $this->user->organization_id,
        'name' => 'report.pdf',
    ]);
});

it('creates a vector store on first upload', function () {
    $file = makeUploadFile('doc.md', 'text/markdown');

    $this->actingAs($this->user)->post(route('knowledge.store'), ['file' => $file]);

    $this->assertDatabaseHas('knowledge_assets', [
        'name' => 'doc.md',
        'provider_store_id' => $this->user->organization->fresh()->vector_store_id,
    ]);
});

it('reuses existing vector store on subsequent uploads', function () {
    $org = $this->user->organization;
    $org->update(['vector_store_id' => 'vs_preexisting']);

    $file = makeUploadFile('another.md', 'text/markdown');

    $this->actingAs($this->user)->post(route('knowledge.store'), ['file' => $file]);

    $this->assertDatabaseHas('knowledge_assets', [
        'name' => 'another.md',
        'provider_store_id' => 'vs_preexisting',
    ]);
});

it('validates file is required', function () {
    $response = $this
        ->actingAs($this->user)
        ->post(route('knowledge.store'), []);

    $response->assertInvalid(['file']);
});

it('validates file type', function () {
    $path = tempnam(sys_get_temp_dir(), 'kbtest_');
    file_put_contents($path, "\x00\x00\x00\x00\x00\x61\x73\x6d\x01\x00\x00\x00");
    $file = new UploadedFile($path, 'script.exe', 'application/x-msdownload', null, true);

    $response = $this
        ->actingAs($this->user)
        ->post(route('knowledge.store'), ['file' => $file]);

    $response->assertSessionHasErrors();
});

it('can delete an asset', function () {
    $asset = KnowledgeAsset::factory()->create([
        'organization_id' => $this->user->organization_id,
        'provider_file_id' => 'file_abc',
        'provider_store_id' => 'vs_store',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->delete(route('knowledge.destroy', $asset));

    $response->assertRedirect(route('knowledge.index'));
    $this->assertDatabaseMissing('knowledge_assets', ['id' => $asset->id]);
});

it('prevents deleting assets from other organizations', function () {
    $otherUser = User::factory()->create();
    $asset = KnowledgeAsset::factory()->create([
        'organization_id' => $otherUser->organization_id,
        'provider_file_id' => 'file_abc',
    ]);

    $response = $this
        ->actingAs($this->user)
        ->delete(route('knowledge.destroy', $asset));

    $response->assertForbidden();
});
