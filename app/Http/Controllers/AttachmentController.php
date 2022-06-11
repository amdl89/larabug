<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttachmentController extends Controller
{
    public function destroy(Request $request, Attachment $attachment)
    {
        Gate::authorize('destroy-attachment', [$attachment]);

        $attachment->deletePreservingMedia();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Attachment Deleted Successfully!'
        ]);

        return back();
    }
}
