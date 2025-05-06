<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->approved) {
            return redirect()->route('verification.notice')
                ->with('message', 'Votre compte est en attente d\'approbation par l\'administrateur.');
        }

        return $next($request);
    }
}
