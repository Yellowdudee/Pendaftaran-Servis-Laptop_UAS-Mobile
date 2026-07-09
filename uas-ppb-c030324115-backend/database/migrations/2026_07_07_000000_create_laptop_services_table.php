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
        Schema::create('laptop_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_name');
            $table->string('serial_number');
            $table->string('phone_number');
            $table->text('complaints');
            $table->enum('status', ['pending', 'proses', 'selesai', 'diambil'])->default('pending');
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->text('technician_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laptop_services');
    }
};
