<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_days', function (Blueprint $table): void {
            $table->id();
            $table->date('date')->unique();
            $table->string('title');
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('attendance_rate', 5, 2)->nullable();
            $table->boolean('is_school_open')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_days');
    }
};
