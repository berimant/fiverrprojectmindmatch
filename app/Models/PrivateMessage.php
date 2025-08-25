<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model {
    protected $fillable = ['sender_id', 'receiver_id', 'content'];
    public $timestamps = false;

        public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}