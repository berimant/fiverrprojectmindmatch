<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_matches_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(0);
            $table->string('mode', 50)->default('offline');
            $table->timestamps();
            $table->unique(['user1_id', 'user2_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
};