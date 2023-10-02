<?php

namespace ALajusticia\Localized\Tests;

use ALajusticia\Localized\Tests\Database\Factories\SubscriptionFactory;
use ALajusticia\Localized\Traits\Expirable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Expirable;
    use HasFactory;

    public $timestamps = false;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SubscriptionFactory::new();
    }
}
