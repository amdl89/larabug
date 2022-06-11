<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Resources\ProjectPriorityResource;
use App\Models\ProjectPriority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectPriorityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'searchQuery' => ['sometimes', 'min:4'],
        ]);

        return inertia('ProjectPriorities/Index', [
            'priorities' => fn () => ProjectPriorityResource::collection(
                ProjectPriority::query()
                    ->withCount(['projects'])
                    ->when(
                        $request->get('searchQuery'),
                        fn ($query, $searchQuery) => $query->searchAndOrder($searchQuery)
                    )
                    ->orderBy('id', 'desc')
                    ->paginate()
            )
        ]);
    }

    public function store(Request $request)
    {
        ProjectPriority::create(
            $request->validate([
                'name' => ['required', 'unique:project_priorities,name'],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Priority Added Successfully!'
        ]);

        return back();
    }

    public function update(Request $request, ProjectPriority $projectPriority)
    {
        $projectPriority->update(
            $request->validate([
                'name' => ['required', Rule::unique('project_priorities', 'name')->ignore($projectPriority->id)],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Priority Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, ProjectPriority $projectPriority)
    {
        $projectPriority->delete();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Priority Deleted Successfully!'
        ]);

        return back();
    }
}
