<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_received_messages', function (Blueprint $table)
        {
            $table->foreign('receiverId')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('messageId')->references('id')->on('messages')->cascadeOnDelete();
        });

        Schema::table('tickets', function (Blueprint $table)
        {
            $table->foreign('priorityId')->references('id')->on('ticket_priorities')->nullOnDelete();
            $table->foreign('typeId')->references('id')->on('ticket_types')->nullOnDelete();
            $table->foreign('creatorId')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigneeId')->references('id')->on('users')->nullOnDelete();
            $table->foreign('projectId')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('project_users', function (Blueprint $table)
        {
            $table->foreign('userId')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('projectId')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('projects', function (Blueprint $table)
        {
            $table->foreign('priorityId')->references('id')->on('project_priorities')->nullOnDelete();
        });

        Schema::table('messages', function (Blueprint $table)
        {
            $table->foreign('senderId')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('change_logs', function (Blueprint $table)
        {
            $table->foreign('initiatorId')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('comments', function (Blueprint $table)
        {
            $table->foreign('userId')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ticketId')->references('id')->on('tickets')->cascadeOnDelete();;
            $table->foreign('parentId')->references('id')->on('comments')->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table)
        {
            $table->foreign('profileId')->references('id')->on('profiles')->cascadeOnDelete();
        });

        Schema::table('attachments', function (Blueprint $table)
        {
            $table->foreign('uploaderId')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_received_messages', function (Blueprint $table)
        {
            $table->dropForeign(['receiverId', 'messageId']);
        });

        Schema::table('tickets', function (Blueprint $table)
        {
            $table->dropForeign(['priorityId', 'typeId', 'creatorId', 'assigneeId', 'projectId']);
        });

        Schema::table('project_users', function (Blueprint $table)
        {
            $table->dropForeign(['userId', 'projectId']);
        });

        Schema::table('projects', function (Blueprint $table)
        {
            $table->dropForeign(['priorityId']);
        });

        Schema::table('messages', function (Blueprint $table)
        {
            $table->dropForeign(['senderId']);
        });

        Schema::table('change_logs', function (Blueprint $table)
        {
            $table->dropForeign(['initiatorId']);
        });

        Schema::table('comments', function (Blueprint $table)
        {
            $table->dropForeign(['userId', 'ticketId', 'parentId']);
        });

        Schema::table('users', function (Blueprint $table)
        {
            $table->dropForeign(['profileId']);
        });

        Schema::table('attachments', function (Blueprint $table)
        {
            $table->dropForeign(['uploaderId']);
        });
    }
}
