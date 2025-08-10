<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'id_category';

    protected $fillable = [
        'category_name',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category', 'id_category', 'id_book');
    }
}
