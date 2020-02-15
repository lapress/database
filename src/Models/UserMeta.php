<?php

namespace LaPress\Database\Models;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class UserMeta extends AbstractMeta
{
    /**
     * @var string
     */
    protected $primaryKey = 'umeta_id';
    /**
     * @var string
     */
    protected $table = 'usermeta';

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
    public function user()
    {
        return $this->belongsTo(ModelResolver::resolve('Term'));
    }
}

