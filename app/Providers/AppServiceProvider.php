<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        View::composer('*', function ($view) {

            $user = Auth::user();

            $wishlistCount = 0;

            if ($user) {
                $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            }

            $view->with([
                'auth_user' => $user,
                'wishlistCount' => $wishlistCount,
            ]);
        });
    }
}
