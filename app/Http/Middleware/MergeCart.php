<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;

class MergeCart
{
    public function handle(Login $event)
    {
        app(CartService::class)->mergeGuestCartToUser();
    }
}
