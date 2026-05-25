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
        Schema::create('company_locations', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        $table->integer('radius_meter')->default(100);
        $table->text('address')->nullable();
        $table->boolean('is_main')->default(false);
        $table->boolean('status')->default(true);
        $table->timestamps();

        // Index
        $table->index('status'); // filter active locations
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_locations');
    }
};
