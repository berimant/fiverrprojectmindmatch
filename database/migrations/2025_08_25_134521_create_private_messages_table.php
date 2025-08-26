
<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_private_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePrivateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            // Use bigint to match the SQL dump
            $table->bigInteger('sender_id')->unsigned();
            $table->bigInteger('receiver_id')->unsigned();
            $table->text('content');
            // Manually define created_at as per the SQL dump
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Define foreign keys to match the SQL dump
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('receiver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_messages');
    }
};