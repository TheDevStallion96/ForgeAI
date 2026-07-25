<?php

use App\Domain\AuthTenant\Models\Organization;
use App\Domain\Marketplace\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Plugin::class)->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->unsignedBigInteger('installed_by')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'plugin_id']);
            $table->index(['organization_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_installations');
    }
};
