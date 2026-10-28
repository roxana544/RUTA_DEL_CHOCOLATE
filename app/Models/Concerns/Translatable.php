<?php

namespace App\Models\Concerns;

trait Translatable
{
    /**
     * Read a "<field>_<locale>" column, falling back to Spanish.
     */
    public function tr(string $field): ?string
    {
        $locale = app()->getLocale();

        return $this->{"{$field}_{$locale}"} ?: $this->{"{$field}_es"};
    }
}
