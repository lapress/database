<?php

namespace LaPress\Database\Models;


use LaPress\Database\ModelResolver;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Post extends AbstractPost
{
    /**
     * @return Category|null
     */
    public function getCategoryAttribute()
    {
        return $this->categories()->first();
    }

    /**
     * @return $this
     */
    public function categories()
    {
        return $this->getTaxonomyRelationship(
            ModelResolver::resolve('Category')
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->getTaxonomyRelationship(
            ModelResolver::resolve('PostTag')
        );
    }
//
    public function scopePublished($query)
    {
        $query->where('post_status', static::STATUS_POST_PUBLISHED)
            ->where('post_date', '<', now());
    }
}
