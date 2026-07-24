<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event_type', 100);
            $table->json('payload')->nullable();
            $table->string('payload_hash', 64);
            $table->string('previous_hash', 64)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at', 0)->nullable();

            $table->index(['organization_id', 'event_type']);
            $table->index(['organization_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
