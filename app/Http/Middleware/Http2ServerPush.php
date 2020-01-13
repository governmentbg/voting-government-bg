<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Http2ServerPush
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if(!$request->ajax()){
            $link = implode(',', app(\App\Libraries\Http2Push::class)->getLinks());
            $this->addLinkHeader($response, $link);
        }

        return $response;
    }

    /**
     * Add Link Header
     *
     * @param \Illuminate\Http\Response $response
     *
     * @param $link
     */
    private function addLinkHeader(Response $response, $link)
    {
        if ($response->headers->get('Link')) {
            $link = $response->headers->get('Link') . ',' . $link;
        }

        $response->header('Link', $link);
    }
}
