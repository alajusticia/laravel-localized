<?php

namespace ALajusticia\Localized\Http\Middleware;

use ALajusticia\Localized\Localized;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class PrefixRoutes
{
    private $localized;

    public function __construct(Localized $localized)
    {
        $this->localized = $localized;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestedLocale = $this->localized->getRequestedLocale();
        $rememberedLocale = $this->localized->getRememberedLocale();

        if (!is_null($rememberedLocale)
            && !is_null($requestedLocale)
            && $requestedLocale !== $rememberedLocale
            && !$request->expectsJson()
            && $request->isMethodSafe()) {

            // Remembered locale takes priority and overrides the locale in URL if different
            $appUrl = rtrim(Config::get('app.url'), '/');
            $currentUrl = $request->fullUrl();
            return redirect(str_replace($appUrl . '/' . $requestedLocale, $appUrl . '/' . $rememberedLocale, $currentUrl));
        }

        return $next($request);
    }
}
