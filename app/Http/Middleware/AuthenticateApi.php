<?php

namespace App\Http\Middleware;

use App\Services\TokenStorage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    public function __construct(
        protected TokenStorage $tokenStorage
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->tokenStorage->hasToken()) {
            return redirect()->route('login');
        }

        // Share token with the request for use in API calls
        $request->attributes->set('api_token', $this->tokenStorage->getToken());

        return $next($request);
    }
}