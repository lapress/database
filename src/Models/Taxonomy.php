<?php

namespace LaPress\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use LaPress\Database\Events\TaxonomySavedEvent;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Taxonomy extends Model
{
    const TAXONOMY_KEY = null;

    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => TaxonomySavedEvent::class
    ];
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['term'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        if (empty(static::TAXONOMY_KEY)) {
            return;
        }

        static::addGlobalScope(static::TAXONOMY_KEY, function (Builder $builder) {
            $builder->whereTaxonomy(static::TAXONOMY_KEY);
        });
    }

    /**
     * Posts
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(ModelResolver::resolve('Post'), 'term_relationships', 'term_taxonomy_id', 'object_id')->recent();
    }

    /**
     * term
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(ModelResolver::resolve('Term'), 'term_id');
    }

    /**
     * Parent
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ModelResolver::resolve('Taxonomy'), 'parent');
    }

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        return optional($this->term)->name;
    }

    /**
     * @return mixed
     */
    public function getSlugAttribute()
    {
        return optional($this->term)->slug;
    }

    /**
     * @return mixed
     */
    public function getIdAttribute()
    {
        return $this->term_taxonomy_id;
    }

    /**
     * @param string $name
     * @param null   $slug
     * @return Taxonomy
     */
    public static function add(string $name, $slug = null): Taxonomy
    {
        $term = ModelResolver::resolve('Term');
        $term = $term::create([
            'name' => $name,
            'slug' => $slug ?: str_slug($name),
        ]);

        return static::create([
            'description' => '',
            'taxonomy'    => static::TAXONOMY_KEY,
            'term_id'     => $term->term_id,
        ]);
    }

    /**
     * @param string $name
     * @return Category|null
     */
    public static function getByName(string $name)
    {
        return static::whereHas('term', function ($query) use ($name) {
            $query->whereSlug($name);
        })->first();
    }

    /**
     * @param $class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToManyPostType($class)
    {
        return $this->belongsToMany(
            $class,
            'term_relationships',
            'term_taxonomy_id',
            'object_id'
        );
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->term->url;
    }

    public function scopePublished($query)
    {
        return $query;
    }

    public function toArray()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->term->name,
            'slug'  => $this->term->slug,
            'urlKey'  => $this->term->slug,
            'count' => [
                'posts' => $this->count
            ],
            'type'  => $this->taxonomy,
        ];
    }
}
