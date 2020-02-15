<?php

namespace LaPress\Database\Models;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class TermMeta extends AbstractMeta
{
    /**
     * @var string
     */
    protected $table = 'termmeta';

    protected $fillable = [
        'meta_value',
        'meta_key',
    ];

    /**
     * Term
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(ModelResolver::resolve('Term'));
    }
}

