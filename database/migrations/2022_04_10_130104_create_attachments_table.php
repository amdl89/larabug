<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table)
        {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->string('attachableType');
            $table->foreignId('attachableId');
            $table->index(["attachableType", "attachableId"]);

            $table->foreignId('uploaderId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
