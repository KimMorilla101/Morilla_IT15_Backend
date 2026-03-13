<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing values before applying enum constraints.
        DB::table('students')
            ->whereNotIn('gender', ['male', 'female'])
            ->update(['gender' => 'male']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY gender ENUM('male','female') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE students MODIFY gender VARCHAR(255) NOT NULL');
        }
    }
};
