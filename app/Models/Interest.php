<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $fillable = ['name'];
    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany(Users::class, 'user_interests', 'interest_id', 'user_id');
    }
}