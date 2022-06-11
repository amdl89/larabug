<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Profile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'title',
        "bio",
        "address",
        "education",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('avatar')
            ->singleFile()
            ->registerMediaConversions(function (Media $media)
            {
                $this->addMediaConversion('avatarThumbnail')
                    ->width(100)
                    ->height(100)
                    ->sharpen(10);
            });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'profileId');
    }
}
