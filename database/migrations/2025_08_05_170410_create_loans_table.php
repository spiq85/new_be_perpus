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
        Schema::create('loans', function (Blueprint $table) {
            $table->id('id_loan');
            $table->foreignId('id_user')
            ->constrained('users', 'id_user')
            ->onDelete('cascade');
            $table->foreignId('id_book')
            ->constrained('books', 'id_book')
            ->onDelete('cascade');
            $table->date('tanggal_peminjaman');
            $table->date('due_date');
            $table->date('tanggal_pengembalian')->nullable();
            $table->enum('status_peminjaman', ['pending', 'ditolak', 'siap_diambil', 'dipinjam', 'dikembalikan', 'terlambat', 'hilang', 'rusak']);
            $table->integer('denda')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
