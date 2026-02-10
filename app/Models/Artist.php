<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{

    protected $fillable = [
        'user_id',
        'stage_name',
        'slug',
        'bio',
        'profile_image',
        'cover_image',
        'genre',
        'location',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'artist_followers');
    }
}

