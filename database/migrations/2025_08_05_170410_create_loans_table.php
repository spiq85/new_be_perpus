<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            // Primary key custom
            $table->id('id_loan');

            // Relasi
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->onDelete('cascade');

            $table->foreignId('id_book')
                ->constrained('books', 'id_book')
                ->onDelete('cascade');

            // Tanggal peminjaman resmi (ketika user udah ambil fisik buku)
            $table->dateTime('tanggal_peminjaman')->nullable();

            // Deadline pengembalian
            $table->dateTime('due_date')->nullable();

            // Tanggal pengembalian FINAL (setelah petugas validasi)
            $table->dateTime('tanggal_pengembalian')->nullable();
            $table->enum('status_peminjaman', [
                'pending',
                'ditolak',
                'siap_diambil',
                'dipinjam',
                'menunggu_validasi_pengembalian',
                'dikembalikan',
                'rusak',
                'hilang',
                'terlambat',
            ])->default('pending');

            // denda (kalau nanti lo mau hitung terlambat / hilang / rusak)
            $table->integer('denda')->default(0);
            
            $table->enum('requested_return_condition', ['baik', 'hilang', 'rusak'])->nullable();

            // catatan bebas dari user
            $table->text('return_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
