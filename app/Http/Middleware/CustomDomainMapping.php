<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\CustomDomainAddon\App\Models\Domain;

class CustomDomainMapping
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Skip for main domain
        if ($host === get_root_domain()) {
            return $next($request);
        }
        
        // Find domain mapping
        $domain = Domain::where('domain', $host)
            ->where('status', 1)
            ->where('is_verified', 1)
            ->first();
            
        if ($domain) {
            // Set business context
            config(['app.current_business_id' => $domain->business_id]);
            session(['mapped_business_id' => $domain->business_id]);
        }
        
        return $next($request);
    }
}