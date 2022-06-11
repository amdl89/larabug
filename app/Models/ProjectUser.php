<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUser extends Pivot
{
    use HasFactory;

    public $incrementing = true;
    protected $table = 'project_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId',
        'projectId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'userId',
        'projectId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'projectId' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
