<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\KnowledgeVector\Models\KnowledgeBase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(KnowledgeBase::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('text');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['knowledge_base_id']);
            $table->index(['organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
