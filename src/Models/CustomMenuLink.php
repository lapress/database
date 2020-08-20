<?php

namespace LaPress\Database\Models;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class CustomMenuLink extends AbstractPost
{
    /**
     * @var string
     */
    protected $postType = 'nav_menu_item';

    /**
     * @var array
     */
    protected $with = ['meta'];

    /**
     * @return null|string
     */
    public function getUrlAttribute(): ?string
    {
        return $this->meta->_menu_item_url;
    }

    public function getUrlKeyAttribute()
    {
        return $this->meta->_menu_item_url;
    }
}
