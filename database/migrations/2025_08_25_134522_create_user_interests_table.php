<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_interests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInterestsTable extends Migration
{
    public function up()
    {
        Schema::create('user_interests', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('interest_id')->constrained('interests')->onDelete('cascade');
            $table->primary(['user_id', 'interest_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_interests');
    }
};