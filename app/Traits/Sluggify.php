<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggify
{
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = $this->generateUniqueSlug($value);
    }

    private function generateUniqueSlug($name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    private function slugExists($slug)
    {
        return static::where('slug', $slug)->where('id', '!=', $this->id ?? null)->exists();
    }
}
