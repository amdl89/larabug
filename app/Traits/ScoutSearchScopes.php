<?php

namespace App\Traits;

trait ScoutSearchScopes
{
    public function scopeSearch($query, string $q)
    {
        $query->whereIn(
            $this->getScoutKeyName(),
            static::search($q)->keys()
        );
    }

    public function scopeSearchAndOrder($query, string $q)
    {
        $query->whereInWithOrder(
            $this->getScoutKeyName(),
            static::search($q)->keys()
        );
    }
}
