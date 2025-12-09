<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Book extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'books';
    protected $primaryKey = 'id_book';

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'publish_year',
        'description',
        'stock',
        'reviews_avg_rating',
        'reviews_count',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'id_book', 'id_category')
            ->withTimestamps();
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
