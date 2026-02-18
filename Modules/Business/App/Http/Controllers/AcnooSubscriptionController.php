<?php

namespace Modules\Business\App\Http\Controllers;

use App\Models\Plan;
use App\Http\Controllers\Controller;

class AcnooSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.permission:subscriptions.read')->only(['index']);
    }

    public function index()
    {
        // Check if user has completed business setup
        if (!auth()->user()->business_id) {
            return redirect()->route('home', ['setup_business' => 1])
                ->with('error', __('Please complete your business setup first.'));
        }

        $plans = Plan::where('status', 1)->latest()->get();
        return view('business::subscriptions.index', compact('plans'));
    }
}
