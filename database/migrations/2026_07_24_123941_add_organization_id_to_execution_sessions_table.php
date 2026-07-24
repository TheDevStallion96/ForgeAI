<?php

use App\Domain\AuthTenant\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('execution_sessions', function (Blueprint $table) {
            $table->foreignIdFor(Organization::class)->after('id')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('execution_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Organization::class);
        });
    }
};
