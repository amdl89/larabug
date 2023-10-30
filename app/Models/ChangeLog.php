<?php

namespace App\Models;

use App\Builders\ChangeLogBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    public $perPage = 10;

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data',
        'date',
        'initiatorId',
        'changableId',
        'changableType',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'changableId',
        'changableType',
        'initiatorId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'data' => 'array',
        'date' => 'datetime',
        'changableId' => 'integer',
        'initiatorId' => 'integer',
    ];

    public function newEloquentBuilder($query)
    {
        return new ChangeLogBuilder($query);
    }

    public function changable()
    {
        return $this->morphTo(__FUNCTION__, 'changableType', 'changableId');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiatorId');
    }
}
