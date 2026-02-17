<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If module is disabled, allow all requests
        if (!moduleCheck('CustomDomainAddon')) {
            return $next($request);
        }

        $host = $request->getHost(); // Current host
        $installedDomain = get_root_domain();

        if (!$installedDomain) {
            abort(406, 'Error: App URL not detected. Please update the APP_URL value in your .env file.');
        }

        // Allow the exact installed domain (main domain)
        if ($host === $installedDomain) {
            return $next($request);
        }

        // Allow localhost and local IPs for development
        $localHosts = ['localhost', '127.0.0.1', '::1'];
        if (in_array($host, $localHosts) || str_starts_with($host, '192.168.') || str_starts_with($host, '10.')) {
            return $next($request);
        }

        // Check if it's a subdomain of the main domain
        if (str_ends_with($host, '.' . $installedDomain)) {
            // It's a subdomain, check if it exists in database
            $domain = \Modules\CustomDomainAddon\App\Models\Domain::query()
                            ->where('domain', $host)
                            ->first();

            if (!$domain) {
                abort(400, 'Error: This subdomain is not registered. Please request for a subdomain from the business panel.');
            }

            // Check if domain is approved
            if ($domain->status != 1 || $domain->is_verified != 1) {
                abort(400, 'Error: This domain is pending approval. Please contact the administrator or approve it from the admin panel.');
            }
        } else {
            // It's a custom domain (addon domain), must be verified
            $domain = \Modules\CustomDomainAddon\App\Models\Domain::query()
                            ->where('domain', $host)
                            ->first();

            if (!$domain) {
                abort(400, 'Error: This domain is not registered. Please request for a custom domain from the business panel.');
            }

            // Check if domain is approved
            if ($domain->status != 1 || $domain->is_verified != 1) {
                abort(400, 'Error: This domain is pending approval. Please contact the administrator or approve it from the admin panel.');
            }
        }

        $publicRoutes = [
            '/',
            'blogs',
            'blogs/*',
            'about-us',
            'plans',
            'data-deletion',
            'terms-conditions',
            'privacy-policy',
            'contact-us',
        ];

        if ($request->is($publicRoutes)) {
            return redirect('/login');
        }

        return $next($request);
    }
}
