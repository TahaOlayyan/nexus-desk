<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('tech_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); 
            $table->enum('category', ['Hardware', 'Software', 'Network']);
            $table->enum('priority', ['P1', 'P2', 'P3']);
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Escalated'])->default('Open');
            $table->text('description');
            $table->string('root_cause')->nullable();
            $table->json('actions_taken')->nullable();
            $table->text('worklog_notes')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
