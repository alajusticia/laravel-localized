<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Use this option to define the available locales in your application.
    |
    */

    'locales' => ['en'],

    /*
    |--------------------------------------------------------------------------
    | Attach locale to user
    |--------------------------------------------------------------------------
    |
    | Set this to true if you want to save the selected locale in the database
    | and allow the locale to be remembered whenever the user is logged in
    | (even if the user is using another device). If enabled, you will need
    | to add the required column to your users table (see the Usage section of
    | the README file).
    |
    */

    'attach_locale_to_user' => false,

    /*
    |--------------------------------------------------------------------------
    | Locale column name
    |--------------------------------------------------------------------------
    |
    | Here you can customize the name of the column added to your users table.
    |
    */

    'locale_column_name' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | Suggest locale
    |--------------------------------------------------------------------------
    |
    | Set this to true if you want to suggest a potentially more appropriate
    | language than the current one. The suggestion occurs when the package
    | finds that the current language is different from the preferred language
    | (determined based on the acceptable languages ordered in the user browser
    | preferences).
    |
    */

    'suggest_locale' => false,

];
