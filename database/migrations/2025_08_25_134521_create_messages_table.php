
<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Use bigint to match the SQL dump
            $table->bigInteger('match_id')->unsigned();
            $table->bigInteger('sender_id')->unsigned();
            $table->text('content');
            // Manually define timestamps to match the SQL dump
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(null);

            // Define foreign keys to match the SQL dump
            $table->foreign('match_id')->references('id')->on('matches');
            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};