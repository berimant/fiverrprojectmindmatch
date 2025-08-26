

<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_matches_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            // Use bigint to match the SQL dump
            $table->bigInteger('user1_id')->unsigned();
            $table->bigInteger('user2_id')->unsigned();
            $table->tinyInteger('is_active')->default(0);
            $table->string('mode', 50)->default('offline');
            // Manually define timestamps to match the SQL dump
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable(false);
            
            // Define unique and foreign keys to match the SQL dump
            $table->unique(['user1_id', 'user2_id']);
            $table->foreign('user1_id')->references('id')->on('users');
            $table->foreign('user2_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
};