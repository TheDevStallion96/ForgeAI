<?php

use App\Domain\Agent\Models\Agent;
use App\Domain\AuthTenant\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Agent::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('active');
            $table->integer('context_tokens_accumulated')->default(0);
            $table->timestamps();

            $table->index(['agent_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_sessions');
    }
};
