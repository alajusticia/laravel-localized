<?php

namespace ALajusticia\Localized\Http\Controllers;

use ALajusticia\Localized\Http\Requests\UpdateLocaleRequest;
use ALajusticia\Localized\Localized;
use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    /**
     * Update the current locale.
     */
    public function update(UpdateLocaleRequest $request, Localized $localized)
    {
        $localized->rememberLocale($request->input('locale'));

        return back();
    }
}
