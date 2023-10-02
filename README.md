# Laravel Localized

Easily localize your Laravel application and provide your users some bonus features like suggesting the best locale for 
them and remembering their preferences.

* [Compatibility](#compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
    * [Switch the language](#switch-the-language)
    * [Suggest appropriate language](#suggest-appropriate-language)
* [License](#license)

## Compatibility

This package has been tested with Laravel 10.

## Installation

1. Install the package via composer using this command:

```bash
composer require alajusticia/laravel-localized
```

2. Publish the configuration file with this command:

```bash
php artisan vendor:publish --provider="ALajusticia\Localized\LocalizedServiceProvider"
```

3. Make your `App\Http\Kernel` class extend the `ALajusticia\Localized\Http\Kernel` class instead of the 
`Illuminate\Foundation\Http\Kernel` class, in order to insert the localization middleware at the right position in the 
middleware priority array (otherwise the redirection of unauthenticated users will fail to add the right locale 
in the URL):

```php
use ALajusticia\Localized\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
```

Or, if you prefer you can do it manually by adding the `ALajusticia\Localized\Http\Middleware\Localize` and
`ALajusticia\Localized\Http\Middleware\PrefixRoutes` middleware after the `Illuminate\Session\Middleware\StartSession`
middleware and before the `Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests` middleware in your 
`App\Http\Kernel` class:

```php
protected $middlewarePriority = [
    //...
    \Illuminate\Session\Middleware\StartSession::class,
    // Insert here ↓
    \ALajusticia\Localized\Http\Middleware\Localize::class,
    \ALajusticia\Localized\Http\Middleware\PrefixRoutes::class,
    // Insert here ↑
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
    // ...
];
```

## Configuration

Make sure you have set the default locale and fallback locale of your application in your `app.php` configuration file:

```php
'locale' => 'en',
'fallback_locale' => 'en',
```

In the `localized.php` configuration file, set the locales that are available and enabled in your application:

```php
'locales' => ['en', 'es', 'fr'],
```

If you are using Laravel Jetstream, modify your `jetstream.php` configuration file according to what you want to achieve:

```php
// If you plan to localize the pages without modifying the URLs
'middleware' => ['web', 'localize'],

// If you want to localize the pages and prefix the routes with the locale
'prefix' => '{locale}',
'middleware' => ['web', 'localize', 'prefixRoutes'],
```

If you are using Laravel Fortify (if you are using Jetstream, you are also using Fortify as it is included with), 
modify your `fortify.php` configuration file according to what you want to achieve:

```php
// If you plan to localize the pages without modifying the URLs
'middleware' => ['web', 'localize'],

// If you want to localize the pages and prefix the routes with the locale
'prefix' => '{locale}',
'middleware' => ['web', 'localize', 'prefixRoutes'],
```

## Usage

If you want to localize the routes and prefix the URLs, use the `localizeAndPrefix` route macro.
Pass a closure with all the routes you want to localize:

```php
use Illuminate\Support\Facades\Route;

Route::localizeAndPrefix(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
    
    // ...
});
```

This method creates a route group with a `locale` parameter prefix and our middleware attached.

Prefixing the URLs is recommended for all the public routes aimed to be indexed by search engines (this way each 
language version has its own URL). For the routes aimed to be used by logged-in users, like the dashboard of a SaaS, 
you could ommit prefixing the URLs if you want to avoid extra logic to be executed (like URL checks and redirects), 
unless you need to keep track of the language used when links are shared. 

If you are prefixing the routes, you can also add a route fallback to catch all the routes without locale and redirect 
to their localized version, using the `fallback` method provided:

```php
Route::fallback(function (Localized $localized) {
    return $localized->fallback();
});
```

To generate localized URLs, just use the `route` method provided by Laravel as you already do, and the `locale` 
parameter will automatically be added:

```php
route('dashboard');

// or with your parameters:
route('posts.show', ['post' => 1]);
```

If you only want to use localization, without prefixing the URLs, use the `localize` route macro instead, it will only 
attach our middleware:

```php
use Illuminate\Support\Facades\Route;

Route::localize(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // ...
});
```

### Select/switch the language

If you are using Blade, you can use the Blade component provided by this package:

```php
<x-localized::language-switcher />
```

This component is a form containing an unstyled select tag, allowing you to add your custom classes. 
All attributes that you will pass through the component's tag will be passed to the underlying select tag.

If you need to create a language switcher in another context (like using Inertia.js), you can 
retrieve the options with the `ALajusticia\Localized\Localized::availableLanguages()` static function. It returns an 
associative array structured as follows: locale => language, the keys being the locale strings and the values being the 
human-readable language native names. In order to switch the language, you can make use of the included route by sending 
the locale in a parameter named `locale` through the `POST` method to the route named `localized.locale.update`.

For a custom implementation or if you need to trigger the language update programmatically, pass the new locale to the 
`rememberLocale` method of the `ALajusticia\Localized\Localized` class. For example, in a controller:

```php
use ALajusticia\Localized\Http\Requests\UpdateLocaleRequest;
use ALajusticia\Localized\Localized;

// ...

public function updateLocale(UpdateLocaleRequest $request, Localized $localized)
{
    $localized->rememberLocale($request->input('locale'));

    return back();
}
```

When a user selects a language, the package will save the related locale in the session with the key
`localizedRememberedLocale`. If you want, you can also configure the package to save the selected locale in the database.
This way, the locale will be saved on the user model and will be remembered whenever the user is logged in (even if the
user is using another device). Enable this feature in the `localized.php` configuration file:

```php
'attach_locale_to_user' => true,
```

and add the `locale` column to your users table:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->locale();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropLocale();
        });
    }
};
```

By default, the name of the database column will be `locale`.
You can customize this name in the `localized.php` configuration file.

### Suggest appropriate language

When the requested language (locale in the URL) is different from the preferred language (configured in the client's 
browser), and no language has yet been explicitly selected and remembered in the session and on the user model, the 
package will set the preferred language in the session in a key named `localizedSuggestedLocale` and in a reponse 
header named `X-Localized-Suggested-Locale`.

Check the existence of this data to display a message with a language suggestion and your own logic to switch to the 
suggested language or continue with the current one, or you can simply add the provided Blade component at the top of 
your pages:

```php
<x-localized::suggestion-banner />
```

## License

Open source, licensed under the [MIT license](LICENSE).
