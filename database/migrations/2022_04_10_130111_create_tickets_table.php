<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table)
        {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('status');
            $table->timestamps();

            $table->foreignId('priorityId')->nullable();

            $table->foreignId('typeId')->nullable();

            $table->foreignId('creatorId')->nullable();

            $table->foreignId('assigneeId')->nullable();

            $table->foreignId('projectId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
