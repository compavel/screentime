<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'title',
        'poster_path',
        'media_type',
        'rating',
        'genre_ids',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
