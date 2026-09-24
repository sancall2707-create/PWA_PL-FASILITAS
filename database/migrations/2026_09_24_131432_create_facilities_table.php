<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Aula SD, Aula SMP, Aula SMA, Lapangan Sekolah
            $table->string('location')->nullable(); // Lokasi/letak ruangan
            $table->integer('capacity')->nullable(); // Kapasitas (untuk input angka fasilitas)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};