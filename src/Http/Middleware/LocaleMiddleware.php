<?php

namespace Ikoncept\Fabriq\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->headers->get('X-LOCALE');

        if (! $locale) {
            $locale = app()->getLocale();
        }

        if ($locale != app()->getLocale()) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
