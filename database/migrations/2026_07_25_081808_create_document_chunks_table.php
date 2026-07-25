<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignId('knowledge_base_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->unsignedInteger('chunk_index');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['knowledge_base_id']);
            $table->index(['chunk_index']);
        });

        DB::statement('ALTER TABLE document_chunks ADD COLUMN embedding vector(1536)');
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
