<?php

namespace LaPress\Database\Models;

use Carbon\Carbon;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class Attachment extends AbstractPost
{
    /**
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @return mixed
     */
    public function image()
    {
        return optional($this->meta()->whereMetaKey('_wp_attachment_metadata')->first())->meta_value;
    }

    /**
     * @return ImageSize
     */
    public function getSizeAttribute()
    {
        return new ImageSize($this->image());
    }

    /**
     * @return null|string
     */
    public function getUrlAttribute(): ?string
    {
        return $this->guid;
    }

    /**
     * @param       $data
     * @param int   $width
     * @param int   $height
     * @param array $meta
     * @return mixed
     */
    public static function add($data, int $width, int $height, $meta = [])
    {
        $now = Carbon::now();
        $base = [
            'post_content_filtered' => '',
            'post_modified'         => $now,
            'post_modified_gmt'     => $now->subHour(),
            'to_ping'               => '',
            'pinged'                => '',
            'post_content'          => '',
            'post_excerpt'          => '',
            'post_status'           => 'inherit',
            'ping_status'           => 'closed',
        ];
        $file = $data['guid'] ?? '';
        $path = config('wordpress.content.url').'/uploads/';
        $data['guid'] = $path.$file;
        $attachment = self::create(array_merge($base, $data));

        $metaData = [
            '_wp_attached_file'       => $file,
            '_wp_attachment_metadata' => serialize([
                'width'      => $width,
                'height'     => $height,
                'file'       => $file,
                'sizes'      => [],
                'image_meta' =>
                    [
                        'aperture'          => '0',
                        'credit'            => '',
                        'camera'            => '',
                        'caption'           => '',
                        'created_timestamp' => '0',
                        'copyright'         => '',
                        'focal_length'      => '0',
                        'iso'               => '0',
                        'shutter_speed'     => '0',
                        'title'             => '',
                        'orientation'       => '0',
                        'keywords'          => [],
                    ],
            ]),
        ];

        $attachment->saveMeta(array_merge($metaData, $meta));

        return $attachment;
    }
}
