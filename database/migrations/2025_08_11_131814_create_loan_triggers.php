<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            CREATE TRIGGER after_loan_update_decrement_stock
            AFTER UPDATE ON loans
            FOR EACH ROW
            BEGIN
                -- Kurangi stok saat status berubah MENJADI 'dipinjam'
                -- dari 'pending' ATAU 'siap_diambil'
                IF NEW.status_peminjaman = 'dipinjam'
                AND OLD.status_peminjaman IN ('pending','siap_diambil') THEN
                    UPDATE books
                    SET stock = CASE WHEN stock > 0 THEN stock - 1 ELSE 0 END
                    WHERE id_book = NEW.id_book;
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER after_loan_update_increment_stock
            AFTER UPDATE ON loans
            FOR EACH ROW
            BEGIN
                -- Naikkan stok hanya ketika betul-betul dikembalikan
                IF NEW.status_peminjaman = 'dikembalikan'
                AND OLD.status_peminjaman IN ('menunggu_validasi_pengembalian','dipinjam','rusak') THEN
                    UPDATE books
                    SET stock = stock + 1
                    WHERE id_book = NEW.id_book;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_loan_update_decrement_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS after_loan_update_increment_stock'); // <- perbaiki nama (huruf kecil semua)
    }
};
