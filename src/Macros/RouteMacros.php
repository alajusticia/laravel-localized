<?php

namespace ALajusticia\Localized\Macros;

use ALajusticia\Localized\Http\Middleware\Localize;
use ALajusticia\Localized\Http\Middleware\PrefixRoutes;
use Illuminate\Support\Facades\Route;

class RouteMacros
{
    /**
     * Add the required timestamp column for the expiration date.
     *
     * @return \Closure
     */
    public function localize()
    {
        return function (callable $closure) {
            return Route::middleware(Localize::class)->group($closure);
        };
    }

    /**
     * Drop the timestamp column added for the expiration date.
     *
     * @return \Closure
     */
    public function localizeAndPrefix()
    {
        return function (callable $closure) {
            return Route::prefix('{locale}')
                ->middleware([Localize::class, PrefixRoutes::class])
                ->group($closure);
        };
    }
}
