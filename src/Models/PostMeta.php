<?php

namespace LaPress\Database\Models;

use LaPress\Database\ModelResolver;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class PostMeta extends AbstractMeta
{
    /**
     * @var string
     */
    protected $table = 'postmeta';

    protected $fillable = [
        'meta_value', 'meta_key'
    ];

    /**
     * post
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(ModelResolver::resolve('Post'));
    }
}
