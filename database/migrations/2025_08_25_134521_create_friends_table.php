
<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_friends_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            // Use bigint to match the SQL dump
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('friend_id')->unsigned();
            // Manually define timestamps to match the SQL dump
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            
            // Define unique and foreign keys to match the SQL dump
            $table->unique(['user_id', 'friend_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('friend_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
};
