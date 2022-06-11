<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Attachment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $perPage = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'notes',
        'attachableId',
        'attachableType',
        'uploaderId'
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'attachableId',
        'attachableType',
        'uploaderId'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'attachableId' => 'integer',
        'uploaderId' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachedFile')
            ->useDisk('attachedFile')
            ->singleFile();
    }

    public function attachable()
    {
        return $this->morphTo(__FUNCTION__, 'attachableType', 'attachableId');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaderId');
    }

    //policy scopes
    public function scopeCanBeViewedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHasMorph(
            'attachable',
            [Ticket::class, Project::class],
            function ($query, $type) use ($user)
            {
                switch ($type)
                {
                    case Ticket::class:
                        $query->whereHas('project.users', fn ($q) => $q->whereKey($user->id));
                        break;

                    case Project::class:
                        $query->whereHas('users', fn ($q) => $q->whereKey($user->id));
                        break;
                }
            }
        )->orWhereHas('uploader', fn ($q) => $q->whereKey($user->id));
    }


    public function scopeCanBeDeletedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHasMorph(
            'attachable',
            [Comment::class, Project::class],
            function ($query, $type) use ($user)
            {
                switch ($type)
                {
                    case Ticket::class:
                        $query->whereHas(
                            'project',
                            fn ($q) => $q->whereHasSupervisor(
                                fn ($query) => $query->whereKey($user->id)
                            )
                        );
                        break;

                    case Project::class:
                        $query->whereHasSupervisor(
                            fn ($query) => $query->whereKey($user->id)
                        );
                        break;
                }
            }
        )->orWhereHas('uploader', fn ($q) => $q->whereKey($user->id));
    }
}
