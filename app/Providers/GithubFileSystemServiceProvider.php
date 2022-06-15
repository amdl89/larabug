<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Radiergummi\FlysystemGitHub\Client;
use Radiergummi\FlysystemGitHub\GitHubAdapter;

class GithubFileSystemServiceProvider extends ServiceProvider
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
        Storage::extend('github', function ($app, $config)
        {
            $client = new Client(
                $config['token'],
                $config['repository'],
                $config['branch'],
            );

            return new Filesystem(new GitHubAdapter($client, $config));
        });
    }
}
