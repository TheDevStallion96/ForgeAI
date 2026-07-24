<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('monthly_limit')->default(1000000);
            $table->bigInteger('current_usage')->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_budgets');
    }
};
