<?php

namespace LaPress\Database\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaPress\Database\Traits\HasMeta;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class MenuItem extends AbstractPost
{
    const META_PARENT_KEY = '_menu_item_menu_item_parent';
    
    use HasMeta;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $postType = 'nav_menu_item';

    /**
     * @return null|string
     */
    public function getAnchorAttribute(): ?string
    {
        return $this->post_title ?: optional($this->instance())->anchor;
    }

    /**
     * @return null|string
     */
    public function getUrlAttribute(): ?string
    {
        return optional($this->instance())->url;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'anchor' => $this->anchor,
            'url'    => $this->url,
        ];
    }

    /**
     * @return Model|null
     */
    public function instance(): ?Model
    {
        $className = $this->getRelationClassName();
        if (!class_exists($className)) {
            return null;
        }
        $instance = app($className);

        if ($instance instanceof Taxonomy) {
            return $instance->whereTermId((int)$this->meta->_menu_item_object_id)->first();
        }

        return $instance->find((int)$this->meta->_menu_item_object_id);
    }

    /**
     * @return mixed
     */
    public function getRelationClassName()
    {
        $key = $this->meta->_menu_item_object;

        return config('wordpress.posts.map.'.$key) ?: $key;
    }

    /**
     * @param string $name
     * @param string $url
     * @param array  $options
     * @return mixed
     */
    public static function addCustom(string $name, string $url, $options = [])
    {
        $post = static::create([
            'post_title' => $name,
        ]);

        $post->saveMeta([
            '_menu_item_type'             => 'custom',
            '_menu_item_menu_item_parent' => 0,
            '_menu_item_object_id'        => $post->ID,
            '_menu_item_object'           => 'custom',
            '_menu_item_target'           => $options['target'] ?? '',
            '_menu_item_classes'          => $options['classess'] ?? '',
            '_menu_item_url'              => $url,
        ]);

        return $post;
    }

    /**
     * @return Collection|null
     */
    public function getItemsAttribute()
    {
        return self::hasMeta(self::META_PARENT_KEY, $this->ID)->orderBy('menu_order')->get();
    }
}
