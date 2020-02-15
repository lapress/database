<?php

namespace LaPress\Database;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class ModelResolver
{
    private const NAMESPACES = [
        'App\Models\\',
        'App\\',
        'LaPress\\Database\\Models\\',
    ];

    /**
     * @param $name
     * @return string
     */
    public static function resolve(string $name)
    {
        foreach (static::NAMESPACES as $namespace) {
            if (class_exists($namespace.$name)) {
                return $namespace.$name;
            }
        }
    }
}
