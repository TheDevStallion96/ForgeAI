<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('architecture_decision_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('title');
            $table->unsignedInteger('adr_number');
            $table->string('status')->default('draft');
            $table->text('context')->nullable();
            $table->text('decision')->nullable();
            $table->text('consequences')->nullable();
            $table->foreignId('superseded_by_id')->nullable()->constrained('architecture_decision_records');
            $table->timestamps();

            $table->unique(['organization_id', 'adr_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('architecture_decision_records');
    }
};
