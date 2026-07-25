<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('git_repositories', function (Blueprint $table) {
            $table->bigInteger('github_id')->nullable()->after('organization_id');
            $table->string('description')->nullable()->after('url');
            $table->boolean('is_private')->default(false)->after('description');
            $table->string('language')->nullable()->after('is_private');
            $table->string('clone_url')->nullable()->after('default_branch');
            $table->string('ssh_url')->nullable()->after('clone_url');
        });
    }

    public function down(): void
    {
        Schema::table('git_repositories', function (Blueprint $table) {
            $table->dropColumn(['github_id', 'description', 'is_private', 'language', 'clone_url', 'ssh_url']);
        });
    }
};
