<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('execution_sessions')->cascadeOnDelete();
            $table->string('tool_name');
            $table->json('parameters');
            $table->json('result')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('requires_hitl')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_executions');
    }
};
