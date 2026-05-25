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
        Schema::create('shifts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->time('start_time');
        $table->time('end_time');
        $table->integer('late_allow_minutes')->default(5);
        $table->integer('early_leave_allow_minutes')->default(5);
        $table->text('description')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamps();

        // Index
        $table->index('status'); // filter active shifts
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
