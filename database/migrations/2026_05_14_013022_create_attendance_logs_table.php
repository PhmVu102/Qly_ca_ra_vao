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
        Schema::create('attendance_logs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')            // foreignId → tự có index
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('work_schedule_id')   // foreignId → tự có index
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->foreignId('company_location_id') // foreignId → tự có index
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->enum('type', ['check_in', 'check_out']);
        $table->dateTime('scan_time');
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        $table->integer('distance_meter')->nullable();
        $table->string('ip_address')->nullable();
        $table->string('device_name')->nullable();
        $table->timestamps();

        // Index
        $table->index('scan_time');              // query log theo thời gian
        $table->index('type');                   // filter check_in / check_out
        $table->index(['user_id', 'scan_time']); // composite: lịch sử 1 nhân viên
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
