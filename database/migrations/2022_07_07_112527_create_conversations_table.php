<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
/* Run the migrations.
*
* @return void
*/
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

/* Reverse the migrations.
*
* @return void
*/
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
