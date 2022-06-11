<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Users/Index', [
            'totalUsersCount' => fn () => User::count(),

            'userRoleChartData' => Role::withCount('users')->get()
                ->pipe(fn ($roles) => [
                    'chartLabels' => $roles->pluck('name'),
                    'labelToDataMap' => $roles->pluck('users_count', 'name'),
                ]),

            'usersAddedThisMonthCount' => fn () => User::query()
                ->whereAddedThisMonth()
                ->count(),

            'users' => fn () => UserResource::collection(
                User::query()
                    ->with([
                        'profile' => fn ($q) => $q->with('media')->select('id', 'name'),
                        'roles' => fn ($q) => $q->select('id', 'name')
                    ])
                    ->when(
                        $request->get('searchQuery'),
                        function ($query, $searchQuery) use ($request)
                        {
                            $request->get('sortBy') ?
                                $query->search($searchQuery) :
                                $query->searchAndOrder($searchQuery);
                        }
                    )
                    ->when(
                        $request->get('filters'),
                        fn ($query, $filters) => $query->filter($filters)
                    )
                    ->when(
                        $request->get('sortBy'),
                        fn ($query, $sortBy) => $query->sort($sortBy),
                        fn ($query) => $query->orderBy('id', 'desc')
                    )
                    ->paginate()
            ),

            'roles' => fn () => RoleResource::collection(Role::all(['id', 'name'])),

            'projects' => fn () => ProjectResource::collection(Project::all(['id', 'title'])),
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        DB::transaction(function () use ($user)
        {
            $user->profile()->delete();
            $user->delete();
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'User Deleted Successfully!'
        ]);

        if ($request->get('redirectUrl'))
        {
            return redirect($request->get('redirectUrl'));
        }
        return back();
    }
}
