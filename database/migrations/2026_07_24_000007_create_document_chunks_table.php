<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\Document;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(KnowledgeBase::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Document::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->text('content');
            $table->unsignedInteger('token_count')->default(0);

            if (config('database.default') === 'pgsql') {
                $table->vector('embedding', dimensions: 1536)->nullable();
            } else {
                $table->json('embedding')->nullable();
            }

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['knowledge_base_id']);
            $table->index(['document_id', 'chunk_index']);
            $table->index(['organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
