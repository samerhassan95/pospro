@extends('layouts.business.master')

@section('title')
    {{ __('Tax Reports') }}
@endsection

@section('main_content')
<div class="min-vh-100">
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="tab-table-container">

                        <div class="table-header p-16 d-print-none">
                            <h4>{{ __('Tax Report List') }}</h4>
                        </div>
                        <div class="table-header justify-content-center border-0 d-none d-block d-print-block text-center">
                            @include('business::print.header')
                            <h4 class="mt-2">{{ __('Tax Report List') }}</h4>
                        </div>

                        <div class="table-top-form p-16">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <form action="{{ route('business.vat-reports.filter') }}" method="post" class="filter-form" table="#vat-sales-data">
                                    @csrf
                                    <input type="hidden" name="tab" id="current-tab" value="sales">
                                    <div class="table-top-left d-flex gap-3 d-print-none">
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="per_page" class="form-control">
                                                <option value="5" selected>{{ __('Show- 5') }}</option>
                                                <option value="10">{{ __('Show- 10') }}</option>
                                                <option value="25">{{ __('Show- 25') }}</option>
                                                <option value="50">{{ __('Show- 50') }}</option>
                                                <option value="100">{{ __('Show- 100') }}</option>
                                            </select>
                                            <span></span>
                                        </div>
                                        <div class="table-search position-relative">
                                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}">
                                            <span class="position-absolute">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-top-btn-group d-print-none">
                                <ul>
                                    <input type="hidden" id="csvBaseUrl" value="{{ route('business.vat.reports.csv') }}">
                                    <input type="hidden" id="excelBaseUrl" value="{{ route('business.vat.reports.excel') }}">

                                    <li>
                                        <a id="csvExportLink" href="#">
                                            <img src="{{ asset('assets/images/logo/csv.svg') }}" alt="CSV">
                                        </a>
                                    </li>
                                    <li>
                                        <a id="excelExportLink" href="#">
                                            <img src="{{ asset('assets/images/logo/excel.svg') }}" alt="Excel">
                                        </a>
                                    </li>

                                    <li>
                                        <a onclick="window.print()" class="print-window">
                                            <img src="{{ asset('assets/images/logo/printer.svg') }}" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="custom-tabs">
                            <button class="tab-item active" onclick="showTab('sales')">{{ __('Sales') }}</button>
                            <button class="tab-item" onclick="showTab('purchase')">{{ __('Purchases') }}</button>
                        </div>

                        <div id="sales" class="tab-content dashboard-tab active">
                            <div id="vat-sales-data">
                                @include('business::reports.vats.sales-datas')
                            </div>
                        </div>

                        <div id="purchase" class="tab-content dashboard-tab">
                            <div id="vat-purchases-data">
                                @include('business::reports.vats.purchases-datas')
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
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
        event.target.classList.add('active');

        // Update hidden input for filter
        document.getElementById('current-tab').value = tabId;

        // Update form table attribute based on tab
        var form = document.querySelector('.filter-form');
        if (tabId === 'sales') {
            form.setAttribute('table', '#vat-sales-data');
        } else {
            form.setAttribute('table', '#vat-purchases-data');
        }
    }
</script>
@endpush
