<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Enums\ReceivedMessageStatus;
use App\Enums\SentMessageStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Traits\ScoutSearchScopes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions, Searchable, ScoutSearchScopes;

    public $perPage = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'profileId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        "profileId",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'profile_name' => $this->profile->name,
        ];
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profileId');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'creatorId');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigneeId');
    }

    public function allProjectTickets()
    {
        return $this->hasManyThrough(
            Ticket::class,
            ProjectUser::class,
            'userId',
            'projectId',
            'id',
            'projectId'
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'userId');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'senderId');
    }

    public function notInTrashSentMessages()
    {
        return $this->sentMessages()
            ->where('sentStatus', SentMessageStatus::Sent);
    }

    public function trashedSentMessages()
    {
        return $this->sentMessages()
            ->where('sentStatus', SentMessageStatus::Trashed);
    }

    public function draftMessages()
    {
        return $this->sentMessages()
            ->withoutGlobalScope('noDrafts')
            ->where('sentStatus', SentMessageStatus::Draft);
    }

    public function changeLogs()
    {
        return $this->hasMany(ChangeLog::class, 'initiatorId');
    }

    public function receivedMessages()
    {
        return $this->belongsToMany(
            Message::class,
            'user_received_messages',
            'receiverId',
            'messageId'
        )
            ->using(UserReceivedMessage::class)
            ->as('receivedMessageInfo');
    }

    public function notInTrashReceivedMessages()
    {
        return $this->receivedMessages()
            ->wherePivotIn(
                'receivedStatus',
                ReceivedMessageStatus::getValues(['Unread', 'Read'])
            );
    }

    public function trashedReceivedMessages()
    {
        return $this->receivedMessages()
            ->wherePivotIn(
                'receivedStatus',
                ReceivedMessageStatus::getValues(['UnreadTrashed', 'ReadTrashed'])
            );
    }

    public function messageReceptions()
    {
        return $this->hasMany(UserReceivedMessage::class, 'receiverId');
    }

    public function notInTrashMessageReceptions()
    {
        return $this->messageReceptions()
            ->whereIn(
                'receivedStatus',
                ReceivedMessageStatus::getValues(['Unread', 'Read'])
            );
    }

    public function trashedMessageReceptions()
    {
        return $this->messageReceptions()
            ->whereIn(
                'receivedStatus',
                ReceivedMessageStatus::getValues(['UnreadTrashed', 'ReadTrashed'])
            );
    }

    public function unreadMessageReceptions()
    {
        return $this->messageReceptions()
            ->where('receivedStatus', ReceivedMessageStatus::Unread);
    }

    public function projects()
    {
        return $this->belongsToMany(
            Project::class,
            'project_users',
            'userId',
            'projectId'
        )
            ->using(ProjectUser::class)
            ->as('projectUsers');
    }

    public function supervisedProjects()
    {
        return $this->projects()
            ->whereHasSupervisor(
                fn ($query) => $query->whereColumn('id', 'project_users.userId')
            );
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'userId');
    }

    // policy scopes
    public function scopeCanViewTicket($query, Ticket $ticket)
    {
        $query->whereRole(UserRole::Admin)
            ->orWhereHas('projects.tickets', fn ($q) => $q->where('tickets.id', $ticket->id));
    }

    public function scopeCanModifyTicket($query, Ticket $ticket)
    {
        $query->whereRole(UserRole::Admin)
            ->orWhereHas('supervisedProjects.tickets', fn ($q) => $q->where('tickets.id', $ticket->id))
            ->orWhereHas('assignedTickets', fn ($q) => $q->where('tickets.id', $ticket->id))
            ->orWhereHas('tickets', fn ($q) => $q->where('tickets.id', $ticket->id));
    }

    public function scopeCanAssignTicket($query, Ticket $ticket)
    {
        $query->whereRole(UserRole::Admin)
            ->orWhereHas('supervisedProjects.tickets', fn ($q) => $q->where('tickets.id', $ticket->id));
    }

    public function scopeCanDeleteTicket($query, Ticket $ticket)
    {
        $query->whereRole(UserRole::Admin)
            ->orWhereHas('supervisedProjects.tickets', fn ($q) => $q->where('tickets.id', $ticket->id));
    }

    public function scopeCanAssignTicketsForOwnProject($query)
    {
        $query->whereRoles([UserRole::Admin, UserRole::PM]);
    }

    public function scopeCanSuperviseAProject($query)
    {
        $query->whereRoles([UserRole::Admin, UserRole::PM]);
    }

    public function scopeCanCreateProjects($query)
    {
        $query->whereRole(UserRole::Admin);
    }

    public function scopeCanBeAssignedTickets($query)
    {
        $query->whereRole(UserRole::Dev);
    }
}
