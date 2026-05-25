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
        Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')       // foreignId → tự có index
            ->nullable()
            ->after('id')
            ->constrained()
            ->nullOnDelete();

        $table->foreignId('department_id') // foreignId → tự có index
            ->nullable()
            ->after('role_id')
            ->constrained()
            ->nullOnDelete();

        $table->string('employee_code')->nullable()->unique(); // unique → tự có index
        $table->string('phone')->nullable();
        $table->string('avatar')->nullable();
        $table->enum('gender', ['male', 'female', 'other'])->nullable();
        $table->date('birth_date')->nullable();
        $table->date('hire_date')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamp('last_login_at')->nullable();

        // Index
        $table->index('status'); // filter active/blocked users
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropIndex(['status']);
        $table->dropForeign(['role_id']);
        $table->dropForeign(['department_id']);

        $table->dropColumn([
            'role_id',
            'department_id',
            'employee_code',
            'phone',
            'avatar',
            'gender',
            'birth_date',
            'hire_date',
            'status',
            'last_login_at',
        ]);
    });

    }
};
