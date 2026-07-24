<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graph_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_node_id')->constrained('graph_nodes')->cascadeOnDelete();
            $table->foreignId('target_node_id')->constrained('graph_nodes')->cascadeOnDelete();
            $table->string('relationship_type');
            $table->decimal('weight', 5, 2)->default(1.0);
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['workflow_id']);
            $table->index(['source_node_id', 'target_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graph_edges');
    }
};
