<?php

namespace ALajusticia\Localized\Listeners;

use ALajusticia\Localized\Localized;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class LoginListener
{
    private $localized;

    /**
     * Create the event listener.
     */
    public function __construct(Localized $localized)
    {
        $this->localized = $localized;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (Config::get('localized.attach_locale_to_user')) {
            $locale = $this->localized->applyLocale($event->user->{Config::get('localized.locale_column_name', 'locale')});
            Cookie::queue('localized_remembered_locale', $locale);
        }
    }
}
