<?php

namespace App\Models\Concerns;

trait HasImage
{
    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
