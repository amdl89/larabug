<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        if (env('APP_ENV') == 'production') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        // Storage::extend(
        //     'google',
        //     function ($app, $config) {
        //         $options = [];

        //         if (!empty($config['teamDriveId'] ?? null)) {
        //             $options['teamDriveId'] = $config['teamDriveId'];
        //         }

        //         $client = new \Google\Client();
        //         $client->setClientId($config['clientId']);
        //         $client->setClientSecret($config['clientSecret']);
        //         $client->refreshToken($config['refreshToken']);

        //         $service = new \Google\Service\Drive($client);
        //         $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
        //         $driver = new \League\Flysystem\Filesystem($adapter);

        //         return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        //     }
        // );
    }
}
