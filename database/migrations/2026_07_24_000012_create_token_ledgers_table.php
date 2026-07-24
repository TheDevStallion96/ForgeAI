<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->string('provider', 50);
            $table->string('model', 100);
            $table->integer('prompt_tokens');
            $table->integer('completion_tokens');
            $table->decimal('estimated_cost_usd', 12, 6);
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
            $table->index(['provider', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_ledgers');
    }
};
