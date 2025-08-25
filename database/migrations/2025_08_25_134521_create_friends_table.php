<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_friends_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendsTable extends Migration
{
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'friend_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('friends');
    }
};
