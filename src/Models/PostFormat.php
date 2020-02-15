<?php

namespace LaPress\Database\Models;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class PostFormat extends Taxonomy
{
    const TAXONOMY_KEY = 'post_format';

    public function getName()
    {
        return str_replace('post-format-', '', $this->name);
    }
}
