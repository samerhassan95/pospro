<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $business = $user->business;
        
        if (!$business) {
            abort(403, 'No business associated with this user.');
        }

        // Check if business plan allows this permission
        if (!$business->allows($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your current plan does not include this feature. Please upgrade your plan.',
                ], 403);
            }

            return redirect()->back()->with('error', 'Your current plan does not include this feature. Please upgrade your plan.');
        }

        return $next($request);
    }
}
