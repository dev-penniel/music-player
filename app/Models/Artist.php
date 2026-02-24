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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Link to the user account
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Albums by this artist
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    // Tracks where artist is the main artist
    public function mainTracks()
    {
        return $this->hasMany(Track::class, 'artist_id');
    }

    // Tracks where artist is featured/producer/etc
    public function tracks()
    {
        return $this->belongsToMany(Track::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Followers of this artist
    public function followers()
    {
        return $this->belongsToMany(User::class, 'artist_followers')
                    ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function profileImageUrl()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/default-artist.jpg');
    }

    public function coverImageUrl()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/default-cover.jpg');
    }

    public function followerCount(): int
    {
        return $this->followers()->count();
    }
}