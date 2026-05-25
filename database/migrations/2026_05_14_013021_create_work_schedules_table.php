<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')     // foreignId → tự có index
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('shift_id')    // foreignId → tự có index
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('location_id') // foreignId → tự có index
            ->nullable()
            ->constrained('company_locations')
            ->nullOnDelete();

        $table->date('work_date');
        $table->text('note')->nullable();
        $table->timestamps();

        // Unique + Index
        $table->unique(['user_id', 'work_date']); // unique → tự có index
        $table->index('work_date');               // filter lịch theo ngày
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
