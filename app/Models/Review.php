<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_review',
        'id_user',
        'id_book',
        'review',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book', 'id_book');
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class, 'id_review', 'id_review');
    }
}
