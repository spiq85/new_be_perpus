<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';
    protected $primaryKey = 'id_loan';

    protected $fillable = [
        'id_user',
        'id_book',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'status_peminjaman',
        'due_date',
        'denda',
        'requested_return_condition',
        'return_note',
    ];

    protected $casts = [
        'tanggal_peminjaman' =>'datetime',
        'due_date' => 'datetime',
        'tanggal_pengembalian' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book', 'id_book');
    }
}
