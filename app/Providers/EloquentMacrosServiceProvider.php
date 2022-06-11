<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder as ELoquentBuilder;
use Illuminate\Support\ServiceProvider;

class EloquentMacrosServiceProvider extends ServiceProvider
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
        ELoquentBuilder::macro(
            'withForeignKeys',
            function ()
            {
                $this->addSelect(
                    $this->getModel()->foreignKeyColumns ?
                        collect($this->getModel()->foreignKeyColumns)
                        ->map(fn ($col) => $this->qualifyColumn($col))
                        ->all()
                        : []
                );

                return $this;
            }
        );
    }
}
