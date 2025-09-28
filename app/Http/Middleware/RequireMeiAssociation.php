<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireMeiAssociation
{
    /**
     * Rotas que o middleware deve ignorar (não exigem mei_id na sessão).
     *
     * @var array
     */
    protected $except = [
        'login',
        'register',
        'comprovante.',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        if (!Auth::check()) {
            return $next($request);
        }

        if (!session()->has('mei_id')) {
            return redirect()->route('profile-mei.index');
        }

        return $next($request);
    }
}
