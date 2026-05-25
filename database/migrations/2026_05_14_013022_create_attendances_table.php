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
        Schema::create('attendances', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')          // foreignId → tự có index
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('work_schedule_id') // foreignId → tự có index
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->date('work_date');

        $table->dateTime('check_in_time')->nullable();
        $table->decimal('check_in_latitude', 10, 8)->nullable();
        $table->decimal('check_in_longitude', 11, 8)->nullable();

        $table->dateTime('check_out_time')->nullable();
        $table->decimal('check_out_latitude', 10, 8)->nullable();
        $table->decimal('check_out_longitude', 11, 8)->nullable();

        $table->integer('late_minutes')->default(0);
        $table->integer('early_leave_minutes')->default(0);
        $table->integer('total_work_minutes')->default(0);

        $table->enum('status', [
            'present',
            'late',
            'early_leave',
            'late_early_leave',
            'absent',
            'incomplete',
        ])->default('absent');

        $table->text('note')->nullable();
        $table->timestamps();

        // Unique + Index
        $table->unique(['user_id', 'work_date']);   // unique → tự có index
        $table->index('work_date');                  // filter theo ngày/tháng
        $table->index('status');                     // filter trạng thái
        $table->index(['work_date', 'status']);      // composite: dashboard hôm nay ai muộn
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
