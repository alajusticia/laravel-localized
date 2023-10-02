<?php

use ALajusticia\Localized\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->post('locale', [LocaleController::class, 'update'])
    ->name('localized.locale.update');
