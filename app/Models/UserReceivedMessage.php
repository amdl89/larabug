<?php

namespace App\Models;

use App\Enums\ReceivedMessageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserReceivedMessage extends Pivot
{
    use HasFactory;

    public $incrementing = true;
    protected $table = 'user_received_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receivedStatus',
        'receiverId',
        'messageId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'receiverId',
        'messageId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'receivedStatus' => ReceivedMessageStatus::class,
        'receiverId' => 'integer',
        'messageId' => 'integer',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'messageId');
    }

    public function receipent()
    {
        return $this->belongsTo(User::class, 'receiverId');
    }
}
