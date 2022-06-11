<?php

namespace App\Traits;

trait WithForeignKeysScope
{
    public function scopewithForeignKeys($query)
    {
        $query->addSelect($this->foreignKeyColumns ?? []);
    }
}
