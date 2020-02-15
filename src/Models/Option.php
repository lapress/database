<?php

namespace LaPress\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Option extends Model
{
    /**
     * @var string
     */
    protected $table = 'options';

    /**
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'option_name',
        'option_value',
    ];

    private static function getCacheKey(string $key)
    {
        return md5('option.'.$key);
    }

    /**
     * @param $value
     */
    public function setOptionValueAttribute($value)
    {
        $this->attributes['option_value'] = is_array($value) ? serialize($value) : $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getOptionValueAttribute($value)
    {
        return @unserialize($value) ?: $value;
    }

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        if (is_array($this->option_value)) {
            return $this->option_value;
        }

        $json = json_decode($this->option_value, true);

        if (json_last_error() == JSON_ERROR_NONE) {
            return $json;
        }

        return $this->option_value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return \Cache::remember(static::getCacheKey($key), config('cache.ttl'), function () use ($key) {
            return optional(static::where('option_name', $key)->first())
                ->value;
        });
    }

    /**
     * @param string $key
     * @return \Illuminate\Support\Collection
     */
    public static function collect($key)
    {
        $collection = collect(static::get($key) ?: []);

        return $collection->map(function ($item) {
            if (is_array($item)) {
                return collect($item);
            }

            return $item;
        });
    }

    public static function set(string $key, $value)
    {
        $option = static::where('option_name', $key)->first() ?: static::create(['option_name'  => $key,
                                                                                 'option_value' => '',
        ]);
        $option->update(['option_value' => $value]);

        \Cache::forget(static::getCacheKey($key));

        return $option;
    }
}
