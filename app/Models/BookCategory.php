<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookCategory extends Pivot
{
    use HasFactory;

    protected $table = 'book_categories';
    protected $primaryKey = 'idbookCategory';

    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'id_book',
        'id_category',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book', 'id_book');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }
}
