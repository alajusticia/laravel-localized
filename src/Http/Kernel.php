<?php

namespace ALajusticia\Localized\Http;

use ALajusticia\Localized\Localized;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;

class Kernel extends HttpKernel
{
    public function __construct(Application $app, Router $router)
    {
        parent::__construct($app, $router);

        // Inject the middleware at the right position
        $this->middlewarePriority = Localized::getMiddlewarePriority($this->middlewarePriority);
    }
}
