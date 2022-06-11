<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReceivedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_received_messages', function (Blueprint $table)
        {
            $table->id();
            $table->integer('receivedStatus');
            $table->timestamps();

            $table->foreignId('receiverId');

            $table->foreignId('messageId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_received_messages');
    }
}
