<?php

namespace App\Models;

use App\Builders\TicketBuilder;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Traits\ScoutSearchScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Ticket extends Model
{
    use HasFactory, Searchable, ScoutSearchScopes;

    public $perPage = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priorityId',
        'typeId',
        'creatorId',
        'assigneeId',
        'projectId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'priorityId',
        'typeId',
        'creatorId',
        'assigneeId',
        'projectId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => TicketStatus::class,
        'priorityId' => 'integer',
        'typeId' => 'integer',
        'creatorId' => 'integer',
        'assigneeId' => 'integer',
        'projectId' => 'integer',
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
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function newEloquentBuilder($query)
    {
        return new TicketBuilder($query);
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class, 'priorityId');
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class, 'typeId');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creatorId');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigneeId');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'ticketId');
    }

    public function topLevelComments()
    {
        return $this->comments()->whereNull('parentId');
    }

    public function attachments()
    {
        return $this->morphMany(
            Attachment::class,
            'attachable',
            'attachableType',
            'attachableId'
        );
    }

    public function changeLogs()
    {
        return $this->morphMany(
            ChangeLog::class,
            'changable',
            'changableType',
            'changableId'
        );
    }

    //policy scopes
    public function scopeCanBeViewedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
        {
            return;
        }

        $query->whereHas('project.users', fn ($q) => $q->whereKey($user->id));
    }

    public function scopeCanBeModifiedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
        {
            return;
        }

        $query->whereHas(
            'project',
            fn ($q) => $q->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )
        )
            ->orWhereHas('assignee', fn ($q) => $q->whereKey($user->id))
            ->orWhereHas('creator', fn ($q) => $q->whereKey($user->id));
    }

    public function scopeCanBeDeletedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
        {
            return;
        }

        $query->whereHas(
            'project',
            fn ($q) => $q->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )
        );
    }

    public function scopeCanBeAssignedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
        {
            return;
        }

        $query->whereHas(
            'project',
            fn ($q) => $q->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )
        );
    }
}
