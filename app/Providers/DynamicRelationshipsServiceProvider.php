<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class DynamicRelationshipsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Project::resolveRelationUsing('supervisor', function ($projectModel)
        {
            return $projectModel->belongsTo(User::class, 'supervisorId');
        });
    }
}
