<?php

namespace ALajusticia\Localized\Http\Middleware;

use ALajusticia\Localized\Localized;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localize
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
        $currentLocale = $this->localized->initLocale();

        $response = $next($request);

        $response->header('X-Localized-Locale', $currentLocale);

        if ($suggestedLocale = $this->localized->getSuggestedLocale()) {
            $response->header('X-Localized-Suggested-Locale', $suggestedLocale);
        }

        return $response;
    }
}
