<?php

namespace App\Models;

use App\Builders\ProjectBuilder;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Traits\ScoutSearchScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable, ScoutSearchScopes;

    public $perPage = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'deadline',
        'priorityId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'priorityId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'deadline' => 'datetime',
        'status' => ProjectStatus::class,
        'priorityId' => 'integer',
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
        return new ProjectBuilder($query);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('coverImage')
            ->useDisk('projectCoverImage')
            ->singleFile()
            ->registerMediaConversions(function (Media $media)
            {
                $this->addMediaConversion('coverImageThumbnail')
                    ->width(600)
                    ->height(300)
                    ->sharpen(10);
            });;
    }

    // dynamic relations: [ supervisor, ]

    public function priority()
    {
        return $this->belongsTo(ProjectPriority::class, 'priorityId');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'project_users',
            'projectId',
            'userId'
        )
            ->using(ProjectUser::class)
            ->as('projectUsers');
    }

    public function devs($constrainOnPivot = false)
    {
        if (!$constrainOnPivot)
        {
            return $this->users()->whereRole(UserRole::Dev);
        }

        return $this->users()
            ->wherePivotIn(
                'userId',
                collect(
                    $this->users()
                        ->whereRole(UserRole::Dev)
                        ->select('users.id')
                        ->toBase()
                        ->get()
                        ->pluck('id')
                )
            );
    }

    public function testers($constrainOnPivot = false)
    {
        if (!$constrainOnPivot)
        {
            return $this->users()->whereRole(UserRole::Tester);
        }

        return $this->users()
            ->wherePivotIn(
                'userId',
                collect(
                    $this->users()
                        ->whereRole(UserRole::Tester)
                        ->select('users.id')
                        ->toBase()
                        ->get()
                        ->pluck('id')
                )
            );
    }

    public function supervisors()
    {
        return $this->users()->canSuperviseAProject();
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'projectId');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'projectId');
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

    public function associateSupervisor($supervisorId = null)
    {
        DB::transaction(function () use ($supervisorId)
        {
            if (!$supervisorId)
            {
                $this->projectUsers()
                    ->whereHas('user', fn ($query) => $query->canSuperviseAProject())
                    ->delete();
                return;
            }
            $this->projectUsers()
                ->whereHas('user', fn ($query) => $query->canSuperviseAProject())
                ->updateOrCreate([], ['userId' => $supervisorId, 'projectId' => $this->id]);
        });
    }

    public function syncDevs(array $devIds)
    {
        $this->devs(true)->sync($devIds);
    }

    public function syncTesters(array $testerIds)
    {
        $this->testers(true)->sync($testerIds);
    }

    // policy scopes
    public function scopeCanHaveTicketAddedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHas('users', fn ($q) => $q->whereKey($user->id));
    }

    public function scopeCanBeViewedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHas('users', fn ($q) => $q->whereKey($user->id));
    }

    public function scopeCanBeModifiedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHasSupervisor(
            fn ($q) => $q->whereKey($user->id)
        );
    }

    public function scopeCanBeDeletedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereRaw('0 = 1');
    }

    public function scopeCanBeAssignedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereRaw('0 = 1');
    }
}
