@extends('layouts.business.master')

@section('title')
{{ __('Dashboard') }}
@endsection

@section('main_content')

{{-- Dashboard Loading Overlay --}}
{{-- <div id="dashboard-loading-overlay" class="dashboard-loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner-large"></div>
        <h4>{{ __('Loading Dashboard...') }}</h4>
    </div>
</div> --}}

@if(auth()->user()->hasPermission('dashboard.read'))
<div class="container-fluid m-h-100">
    @if (env('DEMO_MODE'))
    <div class="alert alert-dismissible fade show text-center mb-3 text-white" role="alert" style="background: linear-gradient(270deg, #ff7db8 0%, #ee2a7b 100%)">
        <b class="text-light">{{ __('Note:') }}</b> {{ __('This is a demo account — data resets every hour for this account only. Some of module are disabled in this account, to get full access please please create your own account.') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
    @endif

    {{-- Dashboard Banner Section --}}
    <div class="row mb-4 align-items-stretch">
        <div class="col-lg-8 col-md-12 mb-3 mb-lg-0 d-flex">
            <div class="dashboard-promo-banner" @if(get_dashboard_banner_image() && get_dashboard_banner_image() !== 'assets/images/dashboard/banner-bg.jpg') style="background-image: url('{{ asset(get_dashboard_banner_image()) }}'); background-size: cover; background-position: center;" @endif>
                <div class="promo-content">
                    <h2>{{ get_dashboard_banner_title() }}</h2>
                    <p>{{ get_dashboard_banner_description() }}</p>
                    <a href="{{ route('business.sales.create') }}" class="promo-btn">
                        {{ get_dashboard_banner_button_text() }}
                        <span class="btn-icon">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.21852 3.1416H8.98739M8.98739 3.1416V6.91047M8.98739 3.1416L2.79485 9.33448" stroke="#09090B" stroke-width="1.25629" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 d-flex">
            <div class="purchase-due-card">
                <h4 class="due-title">{{ __('Purchase Due') }}</h4>
                <div class="due-header">
                    <div class="due-date-display">
                        <span class="due-day" id="selected-day">{{ date('d') }}</span>
                        <div class="due-date-info">
                            <span class="due-weekday" id="selected-weekday">{{ date('D') }}</span>
                            <span class="due-month-year" id="selected-month-year">{{ date('M Y') }}</span>
                        </div>
                    </div>
                    <div class="due-amount">
                        <span class="amount-value" id="due-amount-value">$0</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                    </div>
                </div>
                <div class="calendar-slider">
                    <div class="calendar-nav">
                        <button class="cal-nav-btn" id="cal-prev"><i class="fas fa-chevron-left"></i></button>
                        <span class="cal-month-label" id="cal-month-label">{{ date('F Y') }}</span>
                        <button class="cal-nav-btn" id="cal-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="calendar-days-header">
                        <span>{{ __('M') }}</span><span>{{ __('T') }}</span><span>{{ __('W') }}</span><span>{{ __('T') }}</span><span>{{ __('F') }}</span><span>{{ __('S') }}</span><span>{{ __('S') }}</span>
                    </div>
                    <div class="calendar-days-slider" id="calendar-days-slider">
                        {{-- Days will be populated by JS --}}
                    </div>
                </div>
                <div class="due-note">
                    <span class="note-dot"></span>
                    <span id="due-note-text">{{ __('Select a date to view purchase dues') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row gpt-dashboard-chart mb-30">
        <div class="col-12">
            <div class="business-stat-container">
                <div class="business-stat">
                    <div class="business-content">
                        <div class="custom-image-bg color-1">
                            <img src="{{ asset('assets/images/dashboard/Frame1.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Sales') }}</p>
                        <h4 class="bus-stat-count" id="total_sales">{!! $dashboardData['total_sales'] ?? '0' !!}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-2">
                            <img src="{{ asset('assets/images/dashboard/Frame2.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Purchase') }}</p>
                        <h4 class="bus-stat-count" id="total_purchase">{!! $dashboardData['total_purchase'] ?? '0' !!}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-3">
                            <img src="{{ asset('assets/images/dashboard/Frame3.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Income') }}</p>
                        <h4 class="bus-stat-count" id="total_income">{!! $dashboardData['total_income'] ?? '0' !!}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-4">
                            <img src="{{ asset('assets/images/dashboard/Frame4.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Expense') }}</p>
                        <h4 class="bus-stat-count" id="total_expense">{!! $dashboardData['total_expense'] ?? '0' !!}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-5">
                            <img src="{{ asset('assets/images/dashboard/Frame5.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Customer') }}</p>
                        <h4 class="bus-stat-count" id="total_customer">{{ $dashboardData['total_customer'] ?? '0' }}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-6">
                            <img src="{{ asset('assets/images/dashboard/Frame6.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Total Supplier') }}</p>
                        <h4 class="bus-stat-count" id="total_supplier">{{ $dashboardData['total_supplier'] ?? '0' }}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-7">
                            <img src="{{ asset('assets/images/dashboard/Frame7.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Sales Returns') }}</p>
                        <h4 class="bus-stat-count" id="total_sales_return">{!! $dashboardData['total_sales_return'] ?? '0' !!}</h4>
                    </div>

                    <div class="business-content">
                        <div class="custom-image-bg color-8">
                            <img src="{{ asset('assets/images/dashboard/Frame8.svg') }}" alt="">
                        </div>
                        <p class="bus-stat-title">{{ __('Purchase Returns') }}</p>
                        <h4 class="bus-stat-count" id="total_purchase_return">{!! $dashboardData['total_purchase_return'] ?? '0' !!}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row: Revenue Statistic + Overall Reports --}}
    <div class="row mb-30">
        <div class="col-md-12 col-lg-8">
            <div class="card new-card dashboard-card border-0 p-0 h-100">
                <div class="dashboard-chart">
                    <h4>{{ __('Revenue Statistic') }}</h4>
                    <div class="gpt-up-down-arrow position-relative">
                        <select class="form-control revenue-year">
                            @for ($i = date('Y'); $i >= 2022; $i--)
                            <option @selected($i==date('Y')) value="{{ $i }}">{{ $i }}
                            </option>
                            @endfor
                        </select>
                        <span></span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class=" d-flex align-items-center justify-content-center gap-3 pb-2">
                        <div class="d-flex align-items-center gap-1">
                            <div class="profit-bulet"></div>
                            <p>{{ __('Profit') }}: <strong class="profit-value"></strong></p>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <div class="loss-bulet"></div>
                            <p>{{ __('Loss') }}: <strong class="loss-value"></strong></p>
                        </div>
                    </div>
                    <div class="content">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="card new-card sms-report border-0 p-0 h-100">
                <div class="dashboard-chart">
                    <h4>{{ __('Overall Reports') }}</h4>
                    <div class="gpt-up-down-arrow position-relative">
                        <select class="form-control overview-year">
                            @for ($i = date('Y'); $i >= 2022; $i--)
                            <option @selected($i==date('Y')) value="{{ $i }}">{{ $i }}
                            </option>
                            @endfor
                        </select>
                        <span></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="overallContent row">
                        <div class="col-lg-7">
                            <div style="height: 165px;">
                                <canvas id="Overallreports"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-5 overall-level-container">
                            <div class="d-flex align-items-center gap-1">
                                <div class="purchase-bulet"></div>
                                <p>{{ __('Purchase') }}: <strong id="overall_purchase"></strong></p>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="sales-bulet"></div>
                                <p>{{ __('Sales') }}: <strong id="overall_sale"></strong></p>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="income-bulet"></div>
                                <p>{{ __('Income') }}: <strong id="overall_income"></strong></p>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="expense-bulet"></div>
                                <p>{{ __('Expense') }}: <strong id="overall_expense"></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock and Recent Sales/Purchase in same row --}}
    <div class="row mb-30">
        <div class="col-md-12 col-lg-4">
            <div class="dashborad-table-container p-0 m-0 h-100">
                <div class="dashboard-table-header">
                    <h3>{{ __('Low Stock') }}</h3>
                    <a href="{{ route('business.stocks.index', ['alert_qty' => true]) }}">{{ __('View All') }}<i class="fas fa-chevron-right"></i></a>
                </div>
                <table class="table dashboard-table-content">
                    <thead class="thead-light business-thead">
                        <tr>
                            <th scope="col">{{ __('SL') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col" class="text-center">{{ __('Alert Qty') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $stock->productName }}</td>
                            @if ($stock->stocks_sum_product_stock <= $stock->alert_qty)
                                <td class="text-danger text-center">{{ $stock->stocks_sum_product_stock ?? 0 }}</td>
                            @else
                                <td class="text-success text-center">{{ $stock->stocks_sum_product_stock ?? 0 }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 col-lg-8">
            <div class="tab-table-container h-100">
                <div class="custom-tabs">
                    <button class="tab-item active" onclick="showTab('sales')">{{ __('Recent Sales') }}</button>
                    <button class="tab-item" onclick="showTab('purchase')">{{ __('Recent Purchase') }}</button>
                </div>
                <div id="sales" class="tab-content dashboard-tab active">
                    <div class="table-container">
                        <table class="table dashboard-table-content">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-start" scope="col">{{ __('Date') }}</th>
                                    <th class="text-center" scope="col">{{ __('Invoice') }}</th>
                                    <th class="text-center" scope="col">{{ __('Customer') }}</th>
                                    <th class="text-center" scope="col">{{ __('Total') }}</th>
                                    <th class="text-center" scope="col">{{ __('Paid') }}</th>
                                    <th class="text-center pr-3" scope="col">{{ __('Due') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                <tr>
                                    <td class="text-start">{{ formatted_date($sale->created_at) }}</td>
                                    <td class="text-center">{{ $sale->invoiceNumber }}</td>
                                    <td class="text-center">{{ $sale->party->name ?? '' }}</td>
                                    <td class="text-center">{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</td>
                                    <td class="text-center">{!! currency_format($sale->paidAmount, currency: business_currency()) !!}</td>
                                    <td class="text-center pr-3">{!! currency_format($sale->dueAmount, currency: business_currency()) !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="purchase" class="tab-content dashboard-tab">
                    <div class="table-container">
                        <table class="table dashboard-table-content">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-start" scope="col">{{ __("Date") }}</th>
                                    <th class="text-center" scope="col">{{ __("Invoice") }}</th>
                                    <th class="text-center" scope="col">{{ __("Customer") }}</th>
                                    <th class="text-center" scope="col">{{ __("Total") }}</th>
                                    <th class="text-center" scope="col">{{ __("Paid") }}</th>
                                    <th class="text-center pr-3" scope="col">{{ __("Due") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                <tr>
                                    <td class="text-start">{{ formatted_date($purchase->created_at) }}</td>
                                    <td class="text-center">{{ $purchase->invoiceNumber }}</td>
                                    <td class="text-center">{{ $purchase->party->name ?? '' }}</td>
                                    <td class="text-center">{!! currency_format($purchase->totalAmount, currency: business_currency()) !!}</td>
                                    <td class="text-center">{!! currency_format($purchase->paidAmount, currency: business_currency()) !!}</td>
                                    <td class="text-center pr-3">{!! currency_format($purchase->dueAmount, currency: business_currency()) !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container-fluid">
    <div class="empty-screen-container">
        <div class="empty-screen-content">
            <img src="{{ asset('assets/images/dashboard/empty.svg') }}" alt="" srcset="">
            <p>{{ __('You can not access it!') }}</p>
        </div>
    </div>
</div>
@endif

@php
$currency = business_currency();
@endphp
{{-- Hidden input fields to store currency details --}}
<input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
<input type="hidden" id="currency_position" value="{{ $currency->position }}">
<input type="hidden" id="currency_code" value="{{ $currency->code }}">

<input type="hidden" value="{{ route('business.dashboard.data') }}" id="get-dashboard">
<input type="hidden" value="{{ route('business.dashboard.overall-report') }}" id="get-overall-report">
<input type="hidden" value="{{ route('business.dashboard.revenue') }}" id="revenue-statistic">
@endsection

@push('js')
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/business-dashboard.js') }}?v={{ time() }}"></script>
<script>
    function showTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(tab) {
            tab.classList.remove('active');
        });

        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-item').forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById(tabId).classList.add('active');

        // Add active class to clicked button
        document.querySelector('[onclick="showTab(\'' + tabId + '\')"]').classList.add('active');
    }
</script>
@endpush
