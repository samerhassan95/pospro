<?php

namespace Modules\CustomDomainAddon\App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\CustomDomainAddon\App\Models\Domain;

class DomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.permission:domains.read')->only(['index']);
        $this->middleware('check.permission:domains.create')->only(['store']);
        $this->middleware('check.permission:domains.delete')->only(['destroy', 'deleteAll']);
    }

    public function index()
    {
        $domains = Domain::where('business_id', auth()->user()->business_id)->latest()->paginate(20);
        return view('customdomainaddon::business.index', compact('domains'));
    }

    public function acnooFilter(Request $request)
    {
        $domains = Domain::where('business_id', auth()->user()->business_id)
            ->when(request('search'), function ($q) {
                $q->where('domain', 'like', '%' . request('search') . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        if ($request->ajax()) {
            return response()->json([
                'data' => view('customdomainaddon::business.datas', compact('domains'))->render()
            ]);
        }

        return redirect(url()->previous());
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain_type' => 'required|in:addon,subdomain',
            'domain' => [
                'required',
                'string',
                'unique:domains,domain',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->domain_type === 'subdomain' && str_contains($value, '.')) {
                        $fail(__('Subdomains should not contain dots.'));
                    }
                    if ($request->domain_type === 'addon' && !filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail(__('Invalid domain name : ' . $value));
                    }
                },
            ],
        ]);

        DB::beginTransaction();
        try {
            $planData = plan_data();
            
            if (!$planData || !$planData->plan) {
                return response()->json([
                    'message' => 'No active plan found. Please subscribe to a plan first.',
                ], 400);
            }
            
            $addon_domain_limit = $planData->plan->addon_domain_limit ?? 0;
            $subdomain_limit = $planData->plan->subdomain_limit ?? 0;
            
            // Debug: Log the values
            \Log::info('Domain Limits Check', [
                'plan_id' => $planData->plan_id,
                'plan_name' => $planData->plan->subscriptionName ?? 'N/A',
                'addon_domain_limit' => $addon_domain_limit,
                'subdomain_limit' => $subdomain_limit,
                'plan_object' => $planData->plan->toArray(),
            ]);

            $business = Business::findOrfail(auth()->user()->business_id);
            $addon_domains_count = Domain::addon()->where('business_id', $business->id)->count();
            $subdomains_count = Domain::subdomain()->where('business_id', $business->id)->count();

            if ($request->domain_type == 'addon' && $addon_domains_count >= $addon_domain_limit) {
                return response()->json([
                    'message' => 'Addon domain limit exceeded. Your Current limit is ' . $addon_domain_limit . '. Plan ID: ' . $planData->plan_id,
                ], 400);
            }

            if ($request->domain_type == 'subdomain' && $subdomains_count >= $subdomain_limit) {
                return response()->json([
                    'message' => 'Subdomain limit exceeded. Your Current limit is ' . $subdomain_limit,
                ], 400);
            }

            $status = 0;
            $is_verified = 0;
            $domain_status = 0;
            $is_ssl_enabled = 0;

            $ssl_required = get_option('domain-setting')['ssl_required'] ?? 'off';
            $automatic_approve = get_option('domain-setting')['automatic_approve'] ?? 'off';

            // Check domain availability + HTTP/HTTPS
            if ($automatic_approve === 'on') {
                $domainCheck = checkDomainStatus($request->domain_type === 'addon' ? $request->domain : get_root_domain());

                if ($domainCheck['exists']) {
                    $domain_status = 1;
                }

                if ($domainCheck['https']) {
                    $is_ssl_enabled = 1;
                }

                if ($domain_status && ($ssl_required === 'off' || ($ssl_required === 'on' && $is_ssl_enabled))) {
                    $is_verified = 1;
                }
            }

            // Final status check
            if ($automatic_approve === 'on' && ($request->domain_type === 'subdomain' || ($request->domain_type === 'addon' && ($is_verified || $ssl_required === 'off')))) {
                $status = 1;
            }

            // Save domain
            $domain = Domain::create([
                'business_id'    => $business->id,
                'status'         => $status,
                'is_verified'    => $is_verified,
                'is_ssl_enabled' => $is_ssl_enabled,
                'domain'         => $request->domain_type === 'subdomain'
                    ? $request->domain . '.' . get_root_domain()
                    : $request->domain,
            ]);

            $message = $request->domain_type === 'subdomain'
                ? __('A new sub domain has been added.')
                : __('A new domain has been added.');

            sendNotification($domain->id, route('admin.domains.index', ['id' => $domain->id]), $message);

            DB::commit();

            return response()->json([
                'message'  => $message,
                'redirect' => route('business.domains.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 406);
        }
    }

    public function destroy(string $id)
    {
        Domain::where('id', ($id))->delete();

        return response()->json([
            'message' => __('Domain deleted successfully'),
            'redirect' => route('business.domains.index')
        ]);
    }

    public function deleteAll(Request $request)
    {
        Domain::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => __('Domains deleted successfully'),
            'redirect' => route('business.domains.index')
        ]);
    }
}
