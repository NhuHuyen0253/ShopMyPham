<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Services\CartService;

class MergeGuestCart
{
    public function __construct(protected Request $request, protected CartService $cartService) {}

    public function handle(Login $event): void
    {
        // Gọi currentCart sẽ tự merge guest->user nếu có token
        $this->cartService->currentCart($this->request);
    }
}
