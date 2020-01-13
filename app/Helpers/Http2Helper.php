<?php

if (!function_exists('http2_push_resource')) {
    function http2_push_resouce(string  $path, string $type)
    {
        app(App\Libraries\Http2Push::class)->pushResource($path, $type);
    }
}

if (!function_exists('http2_push_script')) {
    function http2_push_script(string  $path)
    {
        http2_push_resouce($path, 'script');
    }
}

if (!function_exists('http2_push_style')) {
    function http2_push_style(string $path)
    {
        http2_push_resouce($path, 'style');
    }
}

if (!function_exists('http2_push_image')) {
    function http2_push_image(string  $path)
    {
        http2_push_resouce($path, 'image');
    }
}

