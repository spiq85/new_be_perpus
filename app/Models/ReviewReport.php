<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReport extends Model
{
    use HasFactory;

    protected $table = 'review_reports';

    protected $fillable = [
        'id_review',
        'id_user',
        'reason',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'id_review', 'id_review');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
