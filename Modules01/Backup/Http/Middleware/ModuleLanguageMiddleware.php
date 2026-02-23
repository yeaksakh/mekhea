<?php

namespace Modules\Backup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModuleLanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->user()->locale ?? config('app.locale');
        app()->setLocale($locale);
        return $next($request);
    }
}
