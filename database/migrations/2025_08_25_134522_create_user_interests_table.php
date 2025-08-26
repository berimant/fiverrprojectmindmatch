

<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_interests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_interests', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('interest_id')->unsigned();
            $table->primary(['user_id', 'interest_id']);
            
            // Define foreign keys to match the SQL dump
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('interest_id')->references('id')->on('interests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_interests');
    }
};
