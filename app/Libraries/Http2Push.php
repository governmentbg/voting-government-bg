<?php

namespace App\Libraries;

/**
 * Description of Http2Push
 *
 */
class Http2Push
{
    /**
     * @var array
     */
    public $resources = [];

    /**
     * Push a resource onto the queue for the middleware.
     *
     * @param string $path
     * @param string $type
     */
    public function pushResource(string $resourcePath, string $type = null)
    {
        if (!$type) {
            $type = static::getTypeByExtension($resourcePath);
        }
        $this->resources[] = [
            'path' => $resourcePath,
            'type' => $type,
        ];
    }

    /**
     * Generate the server push link strings.
     *
     * @return array
     */
    public function getLinks() : array
    {
        $links = [];
        foreach ($this->resources as $row) {
            $links[] = '<'.$row['path'].'>; rel=preload; as='.$row['type'];
        }
        return $links;
    }
    
    /**
     * @return bool
     */
    public function hasLinks() : bool
    {
        return !empty($this->resources);
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    public static function getTypeByExtension(string $resourcePath) : string
    {
        $parts = explode('.', $resourcePath);
        $extension = end($parts);
        switch ($extension) {
            case 'css': return 'style';
            case 'js': return 'script';
            case 'ttf': return 'font';
            case 'otf': return 'font';
            case 'woff': return 'font';
            case 'woff2': return 'font';
            default: return 'image';
        }
    }
}
