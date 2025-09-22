<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->integer('check_in_accuracy')->nullable(); // GPS accuracy in meters
            $table->integer('check_out_accuracy')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->string('check_in_ip_address', 45)->nullable();
            $table->string('check_out_ip_address', 45)->nullable();
            $table->text('check_in_user_agent')->nullable();
            $table->text('check_out_user_agent')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'early_departure'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'attendance_date', 'shift_id']);
            $table->index(['attendance_date', 'location_id']);
            $table->index(['user_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};