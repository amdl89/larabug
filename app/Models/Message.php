<?php

namespace App\Models;

use App\Builders\MessageBuilder;
use App\Enums\ReceivedMessageStatus;
use App\Enums\SentMessageStatus;
use App\Traits\ScoutSearchScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Message extends Model
{
    use HasFactory, Searchable, ScoutSearchScopes;

    public static function booted()
    {
        static::addGlobalScope(
            'noDrafts',
            fn (Builder $builder) => $builder->where('sentStatus', '<>', SentMessageStatus::Draft)
        );
    }

    public $perPage = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'body',
        'sentStatus',
        'senderId',
    ];

    /**
     * The columns that act as foreign keys
     *
     * @var array
     */
    public $foreignKeyColumns = [
        'senderId',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sentStatus' => SentMessageStatus::class,
        'senderId' => 'integer',
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
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }

    public function newEloquentBuilder($query)
    {
        return new MessageBuilder($query);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'senderId');
    }

    public function receipents()
    {
        return $this->belongsToMany(
            User::class,
            'user_received_messages',
            'messageId',
            'receiverId'
        )
            ->using(UserReceivedMessage::class);
    }

    public function receivedInfo()
    {
        return $this->hasMany(UserReceivedMessage::class, 'messageId');
    }

    public function sendDraft()
    {
        DB::transaction(function ()
        {
            $this->sentStatus = SentMessageStatus::Sent();
            $this->created_at = now();
            $this->save();

            $this->receivedInfo()
                ->update([
                    'created_at' => now(),
                ]);
        });
    }

    public function addRecepients($recepients)
    {
        $this->receipents()
            ->withTimestamps()
            ->attach(
                collect($recepients)
                    ->mapWithKeys(
                        fn ($receipentId) =>
                        [
                            $receipentId => [
                                'receivedStatus' => ReceivedMessageStatus::Unread,
                            ]
                        ]
                    )
                    ->toArray()
            );
    }

    public function syncRecepients($recepients)
    {
        $this->receipents()
            ->withTimestamps()
            ->syncWithPivotValues(
                $recepients,
                ['receivedStatus' => ReceivedMessageStatus::Unread]
            );
    }
}
