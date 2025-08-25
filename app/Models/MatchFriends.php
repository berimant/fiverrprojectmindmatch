<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchFriends extends Model
{
    protected $table = 'matches';
    protected $fillable = ['user1_id', 'user2_id','is_active','mode'];
    public $timestamps = true;
}