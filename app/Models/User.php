<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['phone_number', 'password', 'name', 'profile_status', 'is_online'];
    protected $hidden = ['password'];

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'user_interests', 'user_id', 'interest_id');
    }

    // Relationship with match_friends
    public function matchFriends()
    {
        return $this->hasMany(MatchFriends::class, 'user1_id')->orWhere('user2_id', $this->id);
    }
}