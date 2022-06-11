<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectAttachmentController extends Controller
{
    public function store(StoreAttachmentRequest $request, Project $project)
    {
        Gate::authorize('store-project-attachment', [$project]);

        DB::transaction(function () use ($request, $project)
        {
            $attachment = $project->attachments()
                ->make([
                    'name' => $request->get('name'),
                    'notes' => $request->get('notes'),
                ]);

            $attachment->uploader()->associate($request->user());
            $attachment->save();

            $attachment->addMediaFromRequest('attachedFile')
                ->toMediaCollection('attachedFile');
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Attachment Added Successfully!'
        ]);

        return back();
    }
}
