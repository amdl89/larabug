<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn () => Auth::check() ? new AuthUserResource(Auth::user()) : null,
                'can' => fn () => !Auth::check() ? [] : [
                    'show-user-profile' => Gate::allows('show-user-profile'),
                    'view-dashboard' => Gate::allows('view-dashboard'),
                    'manage-own-message' => Gate::allows('manage-own-message', [$request->user()]),
                    'view-all-ticket' => Gate::allows('view-all-ticket'),
                    'view-all-user-ticket' => Gate::allows('view-all-user-ticket', [$request->user()]),
                    'view-all-all-project-ticket' => Gate::allows('view-all-all-project-ticket', [$request->user()]),
                    'view-all-assigned-ticket' => Gate::allows('view-all-assigned-ticket', [$request->user()]),
                    'view-all-project' => Gate::allows('view-all-project'),
                    'view-all-user-project' => Gate::allows('view-all-user-project', [$request->user()]),
                    'edit-all-project-team' => Gate::allows('edit-all-project-team'),
                    'admin' => Gate::allows('admin'),
                ],
            ],
            'toast' => fn () => $request->session()->get('toast'),
        ]);
    }
}
