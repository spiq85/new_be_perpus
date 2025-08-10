<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';
    protected $primaryKey = 'id_book';

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'publish_year',
        'description',
        'stock',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'id_book', 'id_category');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'id_book', 'id_book');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_book', 'id_book');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'id_book', 'id_book');
    }
}
