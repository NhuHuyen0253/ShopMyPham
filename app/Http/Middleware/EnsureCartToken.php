<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureCartToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookie('cart_token')) {
            Cookie::queue('cart_token', Str::ulid(), 60*24*60, httpOnly: false);
        }
        return $next($request);
    }
}
