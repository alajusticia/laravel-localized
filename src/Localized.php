<?php

namespace ALajusticia\Localized;

use ALajusticia\Localized\Http\Middleware\Localize;
use ALajusticia\Localized\Http\Middleware\PrefixRoutes;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Localized
{
    private $availableLocales;
    private $preferredLocale;
    private $rememberedLocale;
    private $requestedLocale;
    private $suggestedLocale;

    public function __construct()
    {
        $this->availableLocales = Config::get('localized.locales', ['en']);
    }

    private function applyLocale(string $locale): void
    {
        if (!in_array($locale, $this->availableLocales)) {
            $locale = Config::get('app.locale', 'en');
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
    }

    public function initLocale(): string
    {
        $this->preferredLocale = Request::getPreferredLanguage($this->availableLocales);
        $this->rememberedLocale = $this->resolveRememberedLocale();
        $this->requestedLocale = $this->resolveRequestedLocale();

        $locale = $this->rememberedLocale ?: $this->requestedLocale;

        if (is_null($locale)) {
            // If locale not provided or not found in available locales, try to use the preferred language
            $locale = $this->preferredLocale;
        } elseif (Config::get('localized.suggest_locale')) {
            $this->checkPreferredLocale();
        }

        $this->applyLocale($locale);

        return $locale;
    }

    private function resolveRememberedLocale(): ?string
    {
        if (Config::get('localized.attach_locale_to_user') && Auth::user() && !empty(Auth::user()->locale)) {
            $locale = Auth::user()->locale;
        } elseif (!Session::isStarted()) {
            $locale = Request::header('X-Localized-Remembered-Locale');
        } else {
            $locale = Session::get('localizedRememberedLocale');
        }

        return in_array($locale, $this->availableLocales) ? $locale : null;
    }

    private function resolveRequestedLocale(): ?string
    {
        $locale = Request::route('locale') ?: Request::header('X-Localized-Locale');

        return in_array($locale, $this->availableLocales) ? $locale : null;
    }

    public function getAvailableLanguages(): array
    {
        $languages = [];

        foreach ($this->availableLocales as $availableLocale) {
            $languages[$availableLocale] = ucfirst(__('localized::locales.' . $availableLocale, [], $availableLocale));
        }

        return Arr::sort($languages);
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public static function getMiddlewarePriority(array $middlewarePriority)
    {
        $startSessionMiddlewareIndex = array_search(StartSession::class, $middlewarePriority);

        return array_splice($middlewarePriority, $startSessionMiddlewareIndex + 1, 0, [
            Localize::class,
            PrefixRoutes::class,
        ]);
    }

    public function getPreferredLocale(): ?string
    {
        return $this->preferredLocale;
    }

    public function getRememberedLocale() : ?string
    {
        return $this->rememberedLocale;
    }

    public function getRequestedLocale() : ?string
    {
        return $this->requestedLocale;
    }

    public function getSuggestedLocale(): ?string
    {
        return $this->suggestedLocale;
    }

    public function rememberLocale(string $locale): void
    {
        if (in_array($locale, $this->availableLocales)) {
            if (Config::get('localized.attach_locale_to_user') && Auth::user()) {
                Auth::user()->forceFill([Config::get('localized.locale_column_name', 'locale') => $locale])->save();
            }
            if (Session::isStarted()) {
                Session::put('localizedRememberedLocale', $locale);
            }
            $this->applyLocale($locale);
        }
    }

    private function checkPreferredLocale()
    {
        if (Session::isStarted()) {
            Session::forget('localizedSuggestedLocale');
        }

        if (is_null($this->rememberedLocale)
            && !empty($this->preferredLocale)
            && !is_null($this->requestedLocale)
            && $this->requestedLocale !== $this->preferredLocale) {
            $this->suggestedLocale = $this->preferredLocale;
            if (Session::isStarted()) {
                Session::put('localizedSuggestedLocale', $this->preferredLocale);
            }
        }
    }

    public function fallback()
    {
        // Here we check if the locale is present in URL
        // If not, we redirect to the same URL adding the locale
        // Otherwise, we throw a 404 error

        $appUrl = rtrim(Config::get('app.url'), '/');

        $baseUrls = array_map(function($locale) use ($appUrl) {
            return $appUrl . '/' . $locale;
        }, $this->availableLocales);

        $prefixes = array_map(function($locale) use ($appUrl) {
            return $appUrl . '/' . $locale . '/';
        }, $this->availableLocales);

        $currentUrl = Request::fullUrl();

        $isLocalized = in_array($currentUrl, $baseUrls) || Str::startsWith($currentUrl, $prefixes);

        $rememberedLocale = $this->initLocale();

        if (! $isLocalized) {
            $localizedUrl = str_replace($appUrl, $appUrl . '/' . $rememberedLocale, $currentUrl);
            return redirect($localizedUrl);
        }

        abort(404);
    }

    public static function availableLanguages(): array
    {
        return app(Localized::class)->getAvailableLanguages();
    }

    public static function availableLocales(): array
    {
        return app(Localized::class)->getAvailableLocales();
    }

    public static function preferredLocale(): ?string
    {
        return app(Localized::class)->getPreferredLocale();
    }
}
