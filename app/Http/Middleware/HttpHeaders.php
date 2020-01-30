<?php

namespace App\Http\Middleware;

use Closure;

class HttpHeaders
{
    /**
     * Add headers to response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // $contentSecurityPolicyOptions = "default-src 'none';";
        // $contentSecurityPolicyOptions = "form-action 'self';";
        // $response->headers->set('Content-Security-Policy-Report-Only', $contentSecurityPolicyOptions);

        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade'); // https://scotthelme.co.uk/a-new-security-header-referrer-policy/
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains'); // https://scotthelme.co.uk/hsts-the-missing-link-in-tls/
        $response->headers->set('X-Content-Type-Options', 'nosniff'); // https://scotthelme.co.uk/hardening-your-http-response-headers/#x-content-type-options
        $response->headers->set('X-XSS-Protection', '1; mode=block'); // https://scotthelme.co.uk/hardening-your-http-response-headers/#x-content-type-options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // https://scotthelme.co.uk/hardening-your-http-response-headers/#x-frame-options
        //$response->headers->set('Feature-Policy', "vibrate 'self'; usermedia *;"); // https://scotthelme.co.uk/a-new-security-header-feature-policy

        return $response;
    }
}
