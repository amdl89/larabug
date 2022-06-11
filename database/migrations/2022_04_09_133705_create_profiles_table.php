<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table)
        {
            $table->id();
            $table->string('name')->default('Annonymous');
            $table->string('title')->default('No Title Provided');
            $table->text('bio');
            $table->string('address')->default('No Address Provided');
            $table->string('education')->default('No Education Provided');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
