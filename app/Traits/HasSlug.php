<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->slugField()})) {
                $model->{$model->slugField()} = $model->generateUniqueSlug();
            }
        });
    }

    public function generateUniqueSlug(): string
    {
        $slug = Str::slug($this->{$this->slugSource()});
        $originalSlug = $slug;
        $count = 1;

        while (static::where($this->slugField(), $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    protected function slugField(): string
    {
        return 'slug';
    }

    protected function slugSource(): string
    {
        return 'name';
    }
}
