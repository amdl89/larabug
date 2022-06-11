<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteOrphanMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    protected int $batchSize = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cursor = null)
    {
        $this->cursor = $cursor;

        $this->onQueue('deleteOrphanMedia');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mediaRecords = Media::query()
            ->doesntHaveMorph('model', [
                Project::class,
                Ticket::class,
                Attachment::class
            ])
            ->latest('id')
            ->cursorPaginate(
                $this->batchSize,
                ['*'],
                'cursor',
                $this->cursor
            );

        if ($mediaRecords->isNotEmpty())
        {
            collect($mediaRecords->items())->each(
                fn ($media) => DeleteMediaFiles::dispatch($media)
            );
        }

        if ($mediaRecords->nextCursor())
        {
            static::dispatch($mediaRecords->nextCursor());
        }
    }
}
