<?php


namespace App\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * Cart alias
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Cart';
    }
}
