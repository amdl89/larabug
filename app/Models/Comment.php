<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $perPage = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'userId',
        'ticketId',
        'parentId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'userId',
        'ticketId',
        'parentId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'ticketId' => 'integer',
        'parentId' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticketId');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parentId');
    }

    public function nestedParent()
    {
        return $this->parent()->with('nestedParent');
    }

    public function replies()
    {
        return $this->hasMany(static::class, 'parentId');
    }

    public function nestedReplies()
    {
        return $this->replies()->with('nestedReplies');
    }

    // policy scopes
    public function scopeCanBeModifiedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHas('user', fn ($q) => $q->whereKey($user->id));
    }

    public function scopeCanBeDeletedBy($query, User $user)
    {
        if ($user->hasRole(UserRole::Admin))
            return;

        $query->whereHas('user', fn ($q) => $q->whereKey($user->id));
    }
}
