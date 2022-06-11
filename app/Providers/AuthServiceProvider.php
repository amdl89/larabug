<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Policies as Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerGates();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability)
        {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }

    private function registerGates()
    {
        Gate::define('view-dashboard', [Policies\DashBoardPolicy::class, 'view']);
        Gate::define('show-user-profile', [Policies\UserProfilePolicy::class, 'show']);
        Gate::define('update-profile', [Policies\ProfilePolicy::class, 'update']);
        Gate::define('update-profile-avatar', [Policies\ProfileAvatarPolicy::class, 'update']);
        Gate::define('destroy-profile-avatar', [Policies\ProfileAvatarPolicy::class, 'destroy']);
        Gate::define('view-all-ticket', [Policies\TicketPolicy::class, 'viewAll']);
        Gate::define('store-ticket', [Policies\TicketPolicy::class, 'store']);
        Gate::define(
            'store-ticket-assignee-for-project',
            function (User $user, Project $project)
            {
                return  $user->hasRole(UserRole::Admin) ||
                    $project->whereHasSupervisor(
                        fn ($query) => $query->whereKey($user->id)
                    );
            }
        );
        Gate::define('show-ticket', [Policies\TicketPolicy::class, 'show']);
        Gate::define('update-ticket', [Policies\TicketPolicy::class, 'update']);
        Gate::define('destroy-ticket', [Policies\TicketPolicy::class, 'destroy']);
        Gate::define('store-ticket-assignee', [Policies\TicketAssigneePolicy::class, 'store']);
        Gate::define('view-all-project-dev', [Policies\ProjectDevPolicy::class, 'viewAll']);
        Gate::define('update-project-dev', [Policies\ProjectDevPolicy::class, 'update']);
        Gate::define('show-attachment-attached-file', [Policies\AttachmentAttachedFilePolicy::class, 'show']);
        Gate::define('view-all-ticket-top-level-comment', [Policies\TicketTopLevelCommentPolicy::class, 'viewAll']);
        Gate::define('update-comment', [Policies\CommentPolicy::class, 'update']);
        Gate::define('store-comment', [Policies\CommentPolicy::class, 'store']);
        Gate::define('destroy-comment', [Policies\CommentPolicy::class, 'destroy']);
        Gate::define('destroy-attachment', [Policies\AttachmentPolicy::class, 'destroy']);
        Gate::define('store-ticket-attachment', [Policies\TicketAttachmentPolicy::class, 'store']);
        Gate::define('view-all-comment-reply', [Policies\CommentReplyPolicy::class, 'viewAll']);
        Gate::define('view-all-user-ticket', [Policies\UserTicketPolicy::class, 'viewAll']);
        Gate::define('view-all-all-project-ticket', [Policies\UserAllProjectTicketPolicy::class, 'viewAll']);
        Gate::define('view-all-assigned-ticket', [Policies\UserAssignedTicketPolicy::class, 'viewAll']);
        Gate::define('view-all-project', [Policies\ProjectPolicy::class, 'viewAll']);
        Gate::define('show-project', [Policies\ProjectPolicy::class, 'show']);
        Gate::define('store-project', [Policies\ProjectPolicy::class, 'store']);
        Gate::define('update-project', [Policies\ProjectPolicy::class, 'update']);
        Gate::define('destroy-project', [Policies\ProjectPolicy::class, 'destroy']);
        Gate::define('update-project-supervisor', [Policies\ProjectSupervisorPolicy::class, 'update']);
        Gate::define('view-all-user-project', [Policies\UserProjectPolicy::class, 'viewAll']);
        Gate::define('view-all-project-ticket', [Policies\ProjectTicketPolicy::class, 'viewAll']);
        Gate::define('store-project-attachment', [Policies\ProjectAttachmentPolicy::class, 'store']);
        Gate::define('update-project-cover-image', [Policies\ProjectCoverImagePolicy::class, 'update']);
        Gate::define('destroy-project-cover-image', [Policies\ProjectCoverImagePolicy::class, 'destroy']);
        Gate::define('edit-all-project-team', [Policies\AllProjectTeamPolicy::class, 'edit']);
        Gate::define('update-project-tester', [Policies\ProjectTesterPolicy::class, 'update']);
        Gate::define('manage-own-message', fn (User $user, User $routeUser) => $user->is($routeUser));
        Gate::define('admin', fn (User $user) => $user->hasRole(UserRole::Admin));
    }
}
