<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relationship (vehicle atau facility)
            $table->string('bookable_type'); // Vehicle atau Facility
            $table->unsignedBigInteger('bookable_id');
            
            // Kendaraan-specific fields
            $table->string('driver')->nullable(); // Nama driver
            $table->string('purpose')->nullable(); // Keperluan peminjaman
            
            // Tempat/Fasilitas-specific fields
            $table->string('organizer')->nullable(); // Penyelenggara (misal: Gereja Bunda Theresa Cikarang)
            $table->string('event_name')->nullable(); // Nama kegiatan
            $table->text('event_description')->nullable(); // Deskripsi singkat kegiatan
            
            // Universal booking fields
            $table->string('responsible_person'); // Penanggung jawab
            $table->string('responsible_phone'); // No HP/WA Penanggung Jawab
            $table->integer('requested_facilities_qty')->nullable(); // Fasilitas yang dibutuhkan (angka)
            
            // Scheduling
            $table->date('date_from'); // Hari, tanggal mulai
            $table->date('date_to'); // Hari, tanggal selesai
            $table->time('time_from'); // Jam berapa mulai
            $table->time('time_to'); // Sampai jam berapa
            
            // Status tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->enum('confirmation_status', ['sudah', 'belum'])->default('belum'); // Status konfirmasi sekolah
            
            // Additional info
            $table->text('contact_info')->nullable(); // Informasi lebih lanjut hubung: Ibu.... (WA.....)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
