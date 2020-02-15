<?php

namespace LaPress\Database\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Category extends Taxonomy
{
    const TAXONOMY_KEY = 'category';
    /**
     * @var array
     */
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(static::TAXONOMY_KEY, function (Builder $builder) {
            $builder->whereTaxonomy(static::TAXONOMY_KEY);
        });
    }
}
