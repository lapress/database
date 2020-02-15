<?php

namespace LaPress\Database\Models;

use Illuminate\Support\Collection;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class ImageSize
{
    const PATH = '/uploads/%s/';
    /**
     * @var string
     */
    protected $basePath;
    /**
     * @var Collection
     */
    protected $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = collect($data);
        $this->extractBasePath();
    }

    /**
     *
     */
    public function extractBasePath()
    {
        $this->basePath = sprintf(config('wordpress.content.url').static::PATH, dirname($this->data->get('file')));
    }

    /**
     * @param $size
     * @return string
     */
    public function get($size)
    {
        $sizeArray = $this->toArray($size);

        return $this->buildUrl($sizeArray['file']);
    }

    /**
     * @param string $size
     * @return array
     */
    public function toArray(string $size)
    {
        return $this->data->get('sizes')[$size] ?? $this->getFullSizeToArray();
    }

    /**
     * @param string $size
     * @return Collection
     */
    public function collect(string $size)
    {
        return collect($this->toArray($size));
    }

    /**
     * @param string $name
     * @return null|string
     */
    function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return bool
     */
    private function hasSize($name): bool
    {
        return !empty($this->data->get('sizes')[$name]);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function buildUrl(string $fileName): string
    {
        return $this->basePath.$fileName;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->data->get('width');
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->data->get('height');
    }

    /**
     * @return array
     */
    public function getFullSizeToArray(): array
    {
        return [
            'width'  => $this->data->get('width'),
            'height' => $this->data->get('height'),
            'file'   => basename($this->data->get('file')),
        ];
    }
}
