<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table)
        {
            $table->id();
            $table->json('data');
            $table->dateTime('date');
            $table->timestamps();

            $table->string('changableType');
            $table->foreignId('changableId');
            $table->index(["changableType", "changableId"]);

            $table->foreignId('initiatorId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_logs');
    }
}
