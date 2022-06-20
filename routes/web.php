<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'))
    ->name('welcome')
    ->middleware('guest');

Route::middleware('auth')->group(function ()
{
    Route::get('/home', 'DashboardController')
        ->name('home');

    Route::get('/users/{user}/profile', 'UserProfileController@show')
        ->name('users.profiles.show');

    Route::put('/profiles/{profile}', 'ProfileController@update')
        ->name('profiles.update');

    Route::put('profiles/{profile}/avatar', 'ProfileAvatarController@update')
        ->name('profiles.avatar.update');

    Route::delete('profiles/{profile}/avatar', 'ProfileAvatarController@destroy')
        ->name('profiles.avatar.destroy');

    Route::get('/tickets', 'TicketController@index')
        ->name('tickets.index');

    Route::post('/tickets', 'TicketController@store')
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', 'TicketController@show')
        ->name('tickets.show');

    Route::put('/tickets/{ticket}', 'TicketController@update')
        ->name('tickets.update');

    Route::delete('/tickets/{ticket}', 'TicketController@destroy')
        ->name('tickets.destroy');

    Route::post('/tickets/{ticket}/assignees', 'TicketAssigneeController@store')
        ->name('tickets.assignees.store');

    Route::get('/projects/{project}/devs', 'ProjectDevController@index')
        ->name('projects.devs.index');

    Route::put('/projects/{project}/devs', 'ProjectDevController@update')
        ->name('projects.devs.update');

    Route::get(
        'attachments/{attachment}/attachedFiles/show',
        'AttachmentAttachedFileController@show'
    )
        ->name('attachments.attachedFiles.show');

    Route::put('/comments/{comment}', 'CommentController@update')
        ->name('comments.update');

    Route::post('/comments', 'CommentController@store')
        ->name('comments.store');

    Route::delete('/comments/{comment}', 'CommentController@destroy')
        ->name('comments.destroy');

    Route::delete('attachments/{attachment}', 'AttachmentController@destroy')
        ->name('attachments.destroy');

    Route::post('tickets/{ticket}/attachments', 'TicketAttachmentController@store')
        ->name('tickets.attachments.store');

    Route::get('/comments/{comment}/replies', 'CommentReplyController@index')
        ->name('comments.replies.index');

    Route::get('/tickets/{ticket}/topLevelComments', 'TicketTopLevelCommentController@index')
        ->name('tickets.topLevelComments.index');

    Route::get('/users/{user}/tickets', 'UserTicketController@index')
        ->name('users.tickets.index');

    Route::get('/users/{user}/allProjects/tickets', 'UserAllProjectTicketController@index')
        ->name('users.allProjects.tickets.index');

    Route::get('/users/{user}/assignedTickets', 'UserAssignedTicketController@index')
        ->name('users.assignedTickets.index');

    Route::get('/projects', 'ProjectController@index')
        ->name('projects.index');

    Route::get('/projects/{project}', 'ProjectController@show')
        ->name('projects.show');

    Route::post('/projects', 'ProjectController@store')
        ->name('projects.store');

    Route::put('/projects/{project}', 'ProjectController@update')
        ->name('projects.update');

    Route::delete('/projects/{project}', 'ProjectController@destroy')
        ->name('projects.destroy');

    Route::post('/projects/{project}/supervisors', 'ProjectSupervisorController@store')
        ->name('projects.supervisors.store');

    Route::get('/users/{user}/projects', 'UserProjectController@index')
        ->name('users.projects.index');

    Route::get('/projects/{project}/tickets', 'ProjectTicketController@index')
        ->name('projects.tickets.index');

    Route::post('projects/{project}/attachments', 'ProjectAttachmentController@store')
        ->name('projects.attachments.store');

    Route::put('projects/{project}/coverImage', 'ProjectCoverImageController@update')
        ->name('projects.coverImage.update');

    Route::delete('projects/{project}/coverImage', 'ProjectCoverImageController@destroy')
        ->name('projects.coverImage.destroy');

    Route::get('/allProjects/teams/edit', 'AllProjectTeamController@edit')
        ->name('allProjects.teams.edit');

    Route::put('/projects/{project}/testers', 'ProjectTesterController@update')
        ->name('projects.testers.update');

    Route::middleware('admin')->group(function ()
    {
        Route::get('/ticketPriorities', 'TicketPriorityController@index')
            ->name('ticketPriorities.index');

        Route::post('/ticketPriorities', 'TicketPriorityController@store')
            ->name('ticketPriorities.store');

        Route::put('/ticketPriorities/{ticketPriority}', 'TicketPriorityController@update')
            ->name('ticketPriorities.update');

        Route::delete('/ticketPriorities/{ticketPriority}', 'TicketPriorityController@destroy')
            ->name('ticketPriorities.destroy');

        Route::get('/ticketTypes', 'TicketTypeController@index')
            ->name('ticketTypes.index');

        Route::post('/ticketTypes', 'TicketTypeController@store')
            ->name('ticketTypes.store');

        Route::put('/ticketTypes/{ticketType}', 'TicketTypeController@update')
            ->name('ticketTypes.update');

        Route::delete('/ticketTypes/{ticketType}', 'TicketTypeController@destroy')
            ->name('ticketTypes.destroy');

        Route::get('/projectPriorities', 'ProjectPriorityController@index')
            ->name('projectPriorities.index');

        Route::post('/projectPriorities', 'ProjectPriorityController@store')
            ->name('projectPriorities.store');

        Route::put('/projectPriorities/{projectPriority}', 'ProjectPriorityController@update')
            ->name('projectPriorities.update');

        Route::delete('/projectPriorities/{projectPriority}', 'ProjectPriorityController@destroy')
            ->name('projectPriorities.destroy');

        Route::get('/users', 'UserController@index')
            ->name('users.index');

        Route::delete('/users/{user}', 'UserController@destroy')
            ->name('users.destroy');

        Route::put('/users/{user}/role', 'UserRoleController@update')
            ->name('users.roles.update');
    });

    Route::middleware('can-manage-own-message')->group(function ()
    {
        Route::get('/users/{user}/notInTrashReceivedMessages', 'UserNotInTrashReceivedMessageController@index')
            ->name('users.notInTrashReceivedMessages.index');

        Route::post('/users/{user}/notInTrashReceivedMessages', 'UserNotInTrashReceivedMessageController@storeAll')
            ->name('users.notInTrashReceivedMessages.storeAll');

        Route::get('/users/{user}/notInTrashSentMessages', 'UserNotInTrashSentMessageController@index')
            ->name('users.notInTrashSentMessages.index');

        Route::post('/users/{user}/notInTrashSentMessages', 'UserNotInTrashSentMessageController@storeAll')
            ->name('users.notInTrashSentMessages.storeAll');

        Route::get('/users/{user}/draftMessages', 'UserDraftMessageController@index')
            ->name('users.draftMessages.index');

        Route::put('/users/{user}/draftMessages/{message}', 'UserDraftMessageController@update')
            ->name('users.draftMessages.update');

        Route::delete('/users/{user}/draftMessages', 'UserDraftMessageController@destroyAll')
            ->name('users.draftMessages.destroyAll');

        Route::get('/users/{user}/trashedMessages', 'UserTrashedMessageController@index')
            ->name('users.trashedMessages.index');

        Route::get('/users/{user}/trashedReceivedMessages', 'UserTrashedReceivedMessageController@index')
            ->name('users.trashedReceivedMessages.index');

        Route::post('/users/{user}/trashedReceivedMessages', 'UserTrashedReceivedMessageController@storeAll')
            ->name('users.trashedReceivedMessages.storeAll');

        Route::delete('/users/{user}/trashedReceivedMessages', 'UserTrashedReceivedMessageController@destroyAll')
            ->name('users.trashedReceivedMessages.destroyAll');

        Route::post('/users/{user}/readReceivedMessages', 'UserReadReceivedMessageController@storeAll')
            ->name('users.readReceivedMessages.storeAll');

        Route::post(
            '/users/{user}/notInTrashMessageReceptions/{messageReception}',
            'UserNotInTrashMessageReceptionController@store'
        )
            ->name('users.notInTrashMessageReceptions.store');

        Route::post(
            '/users/{user}/trashedMessageReceptions/{messageReception}',
            'UserTrashedMessageReceptionController@store'
        )
            ->name('users.trashedMessageReceptions.store');

        Route::delete(
            '/users/{user}/trashedMessageReceptions/{messageReception}',
            'UserTrashedMessageReceptionController@destroy'
        )
            ->name('users.trashedMessageReceptions.destroy');

        Route::post(
            '/users/{user}/readMessageReceptions/{messageReception}',
            'UserReadMessageReceptionController@store'
        )
            ->name('users.readMessageReceptions.store');

        Route::get(
            '/users/{user}/trashedSentMessages',
            'UserTrashedSentMessageController@index'
        )
            ->name('users.trashedSentMessages.index');

        Route::post(
            '/users/{user}/trashedSentMessages/{message}',
            'UserTrashedSentMessageController@store'
        )
            ->name('users.trashedSentMessages.store');

        Route::post(
            '/users/{user}/trashedSentMessages',
            'UserTrashedSentMessageController@storeAll'
        )
            ->name('users.trashedSentMessages.storeAll');

        Route::delete(
            '/users/{user}/trashedSentMessages',
            'UserTrashedSentMessageController@destroyAll'
        )
            ->name('users.trashedSentMessages.destroyAll');

        Route::delete(
            '/users/{user}/trashedSentMessages/{message}',
            'UserTrashedSentMessageController@destroy'
        )
            ->name('users.trashedSentMessages.destroy');

        Route::post('users/{user}/draftMessages/send', 'UserSendDraftMessageActionController')
            ->name('users.draftMessages.send');

        Route::post(
            '/users/{user}/restoredSentMessages',
            'UserRestoredSentMessageController@store'
        )
            ->name('users.restoredSentMessages.store');

        Route::get('/users/{user}/possibleMessageRecepients', 'UserPossibleMessageRecepientsController@index')
            ->name('users.possibleMessageRecepients.index');

        Route::post('/users/{user}/sentMessages', 'UserSentMessageController@store')
            ->name('users.sentMessages.store');
    });
});

if (app()->environment('local'))
{
    Route::get('/test', function ()
    {
    });
}
