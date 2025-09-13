<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CounselorOrAssistantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (!$request->user()->isCounselor() && !$request->user()->isAssistant())) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. Only counselors and assistants can access this feature.');
        }

        return $next($request);
    }
}
