<?php

namespace LaPress\Database\Traits;

use Illuminate\Database\Eloquent\Builder;
use LaPress\Database\Post;
use LaPress\Database\PostMeta;
use LaPress\Database\Term;
use LaPress\Database\TermMeta;
use LaPress\Database\User;
use LaPress\Database\UserMeta;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
trait HasMeta
{
    /**
     * @var array
     */
    protected $metaModels = [
        Post::class => PostMeta::class,
        Term::class => TermMeta::class,
        User::class => UserMeta::class,
    ];

    /**
     * @return null|string
     */
    public function getMetableClass(): ?string
    {
        $key = $this->getMetableClassKey();

        return $this->metaModels[$key];
    }

    /**
     * @return string
     */
    public function getMetableClassKey()
    {
        $key = get_class($this);

        if (array_key_exists($key, $this->metaModels)) {
            return $key;
        }

        if (array_key_exists(get_parent_class($key), $this->metaModels)) {
            return get_parent_class($key);
        }

        return Post::class;
    }


    /**
     * @return null|string
     */
    public function getMetableKeyName(): ?string
    {
        $key = str_replace('Meta', '', $this->getMetableClass());

        return strtolower(class_basename($key)).'_id';
    }

    /**
     * @param Builder $query
     * @param         $meta
     * @param null    $value
     * @return Builder
     */
    public function scopeHasMeta(Builder $query, $meta, $value = null)
    {
        $meta = is_array($meta) ? $meta : [$meta => $value];

        foreach ($meta as $key => $value) {
            $query->whereHas('meta', function ($query) use ($key, $value) {
                if (is_string($key)) {
                    $query->where('meta_key', $key);

                    return is_null($value) ? $query : $query->where('meta_value', $value);
                }

                return $query->where('meta_key', $value);
            });
        }

        return $query;
    }

    public function scopeHasNotMeta(Builder $query, $meta, $value = null)
    {
        $meta = is_array($meta) ? $meta : [$meta => $value];

        foreach ($meta as $key => $value) {
            $query->whereHas('meta', function ($query) use ($key, $value) {
                if (is_string($key)) {
                    $query->where('meta_key', $key);

                    return is_null($value) ? $query : $query->where('meta_value', '!=',$value);
                }

                return $query->where('meta_key', $value);
            });
        }

        return $query;
    }

    /**
     * @return mixed
     */
    public function meta()
    {
        return $this->hasMany($this->getMetableClass(), $this->getMetableKeyName());
    }

    /**
     * @param      $payload
     * @param null $value
     * @return $this
     */
    public function saveMeta($payload, $value = null)
    {
        if (!is_array($payload)) {
            $payload = [$payload => $value];
        }

        foreach ($payload as $key => $value) {
            $this->saveMetaKey($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param        $value
     * @return mixed
     */
    public function saveMetaKey(string $key, $value)
    {
        $meta = $this->meta()->where('meta_key', $key)
                     ->firstOrNew(['meta_key' => $key]);

        $value = is_array($value) ? serialize($value) : $value;

        $result = $meta->fill(['meta_value' => $value])->save();

        $this->load('meta');

        return $result;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function incrementMetaKey(string $key)
    {
        $meta = $this->meta()->where('meta_key', $key)
                     ->firstOrNew(['meta_key' => $key]);

        if (is_null($meta->meta_value)) {
            $meta->fill(['meta_value' => 0])->save();
        }

        $meta->increment('meta_value');

        return $meta->toArray();
    }
}
