<?php

namespace ALajusticia\Localized\Macros;

use Illuminate\Support\Facades\Config;

class BlueprintMacros
{
    /**
     * Add the required timestamp column for the expiration date.
     *
     * @return \Closure
     */
    public function locale()
    {
        return function ($columnName = null) {
            return $this->string($columnName ?: Config::get('localized.locale_column_name', 'locale'))->nullable();
        };
    }

    /**
     * Drop the timestamp column added for the expiration date.
     *
     * @return \Closure
     */
    public function dropLocale()
    {
        return function ($columnName = null) {
            return $this->dropColumn($columnName ?: Config::get('localized.locale_column_name', 'locale'));
        };
    }
}
