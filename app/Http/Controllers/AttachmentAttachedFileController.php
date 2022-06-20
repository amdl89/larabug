<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Gate;

class AttachmentAttachedFileController extends Controller
{
    public function show(Attachment $attachment)
    {
        Gate::authorize('show-attachment-attached-file', [$attachment]);

        return $attachment->getFirstMedia('attachedFile');
    }
}
