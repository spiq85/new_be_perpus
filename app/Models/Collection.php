<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $table = 'collections';

    protected $primaryKey = 'id_collection';

    protected $fillable = [
        'id_user',
        'id_book',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book', 'id_book');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
