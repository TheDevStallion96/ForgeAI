<?php

use App\Domain\Agent\Models\ExecutionSession;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExecutionSession::class, 'session_id')->constrained('execution_sessions')->cascadeOnDelete();
            $table->string('role', 20);
            $table->text('content')->nullable();
            $table->json('tool_calls')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_messages');
    }
};
