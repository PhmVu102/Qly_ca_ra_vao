<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_schedules', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique(['user_id', 'work_date']);
            $table->unique(['user_id', 'work_date', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::table('work_schedules', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'work_date', 'shift_id']);
            $table->unique(['user_id', 'work_date']);
            $table->dropIndex(['user_id']);
        });
    }
};
