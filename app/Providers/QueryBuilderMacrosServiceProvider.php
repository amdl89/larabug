<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

class QueryBuilderMacrosServiceProvider extends ServiceProvider
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
        QueryBuilder::macro(
            'orderByPosition',
            function (string $keyFieldName, iterable $keys, string $order = 'asc')
            {
                if (collect($keys)->isEmpty())
                {
                    return $this;
                }
                $keysParamString = collect($keys)->join(', ');

                return $this->orderByRaw("FIELD({$keyFieldName}, {$keysParamString}) {$order}");
            }
        );

        QueryBuilder::macro(
            'whereInWithOrder',
            function (string $keyFieldName, iterable $keys)
            {
                if (collect($keys)->isEmpty())
                {
                    return $this->whereRaw('0 = 1');
                }

                return $this
                    ->wherein($keyFieldName, $keys)
                    ->orderByPosition($keyFieldName, $keys);
            }
        );
    }
}
