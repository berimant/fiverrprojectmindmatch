<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 15)->unique();
            $table->string('password');
            $table->string('name')->nullable();
            $table->enum('profile_status', ['anonim', 'terbuka'])->default('anonim');
            $table->boolean('is_online')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};