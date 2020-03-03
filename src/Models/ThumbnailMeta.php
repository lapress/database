<?php

namespace LaPress\Database\Models;

use Illuminate\Support\Arr;
use LaPress\Database\ModelResolver;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class ThumbnailMeta extends PostMeta
{
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_FULL = 'full';

    /**
     * @var array
     */
    protected $with = ['attachment'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attachment()
    {
        return $this->belongsTo(ModelResolver::resolve('Attachment'), 'meta_value');
    }

    public function getSizeAttribute()
    {
        return $this->attachment->size;
    }

    /**
     * @param string $size
     * @return array
     * @throws \Exception
     */
    public function size($size)
    {
        if ($size == self::SIZE_FULL) {
            return $this->attachment->url;
        }

        $meta = $this->attachment->meta->_wp_attachment_metadata;

        $sizes = Arr::get($meta, 'sizes');

        if (!isset($sizes[$size])) {
            return $this->attachment->url;
        }


        $data = Arr::get($sizes, $size);

        return array_merge($data, [
            'url' => dirname($this->attachment->url).'/'.$data['file'],
        ])['url'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->attachment->guid;
    }
}
