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
        DB::unprepared(
            'CREATE TRIGGER after_loan_update_decrement_stock
            AFTER UPDATE ON loans
            FOR EACH ROW
            BEGIN
            IF NEW.status_peminjaman = "dipinjam" AND OLD.status_peminjaman = "pending" THEN
            UPDATE books SET stock = stock - 1 WHERE id_book = NEW.id_book;
            END IF;
            END'
        );

        DB::unprepared(
            'CREATE TRIGGER after_loan_update_increment_stock
            AFTER UPDATE ON loans
            FOR EACH ROW
            BEGIN
            IF NEW.status_peminjaman = "dikembalikan" THEN
            UPDATE books SET stock = stock + 1 WHERE id_book = NEW.id_book;
            END IF;
            END'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_loan_update_decrement_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS after_loan_update_increment_Stock');
    }
};
