<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['user_id']);

            // Xóa unique constraint
            $table->dropUnique('attendances_user_id_work_date_unique');

            // Tạo lại foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unique(['user_id', 'work_date']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
