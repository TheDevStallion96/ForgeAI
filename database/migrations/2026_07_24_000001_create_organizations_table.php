<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->bigInteger('monthly_token_budget')->default(1000000);
            $table->timestamps();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->nullOnDelete();
        });

        $teams = DB::table('teams')->get();

        foreach ($teams as $team) {
            $name = $team->name;
            if (str_ends_with($name, "'s Team")) {
                $name = substr($name, 0, -8);
            }

            $organizationId = DB::table('organizations')->insertGetId([
                'name' => $name,
                'slug' => $team->slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('teams')
                ->where('id', $team->id)
                ->update(['organization_id' => $organizationId]);

            DB::table('users')
                ->join('team_members', 'users.id', '=', 'team_members.user_id')
                ->where('team_members.team_id', $team->id)
                ->update(['users.organization_id' => $organizationId]);
        }
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::dropIfExists('organizations');
    }
};
