<?php

namespace LaPress\Database\Models;

use Illuminate\Database\Eloquent\Model;
use LaPress\Database\Traits\HasMeta;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Term extends Model
{
    use HasMeta;
    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Taxonomy
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(ModelResolver::resolve('Taxonomy'), 'term_id');
    }

    /**
     * @return string|null
     */
    public function getAnchorAttribute(): ?string
    {
        return $this->name;
    }
}
