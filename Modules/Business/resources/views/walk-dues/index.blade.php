@extends('layouts.business.master')

@section('title')
    {{ __('Walk-in Customer Due') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16 d-print-none">
                        <h4>{{ __('Walk-in Customer Due') }}</h4>
                    </div>

                    <div class="table-header justify-content-center border-0 text-center d-none d-block d-print-block">
                        @include('business::print.header')
                        <h4 class="mt-2">{{ __('Walk-in Customer Due') }}</h4>
                    </div>

                    <div class="table-top-form p-16">
                        <form action="{{ route('business.walk-dues.filter') }}" method="post" class="filter-form" table="#walk-due-table">
                            @csrf
                            <div class="table-top-left d-flex gap-3">
                                <div class="table-search position-relative d-print-none">
                                    <input class="form-control searchInput" type="text" name="search"
                                        placeholder="{{ __('Search by invoice, customer name or phone...') }}" value="{{ request('search') }}">
                                    <span class="position-absolute">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>

                                <div class="table-search position-relative d-print-none">
                                    <input class="form-control" type="date" name="start_date" 
                                        placeholder="{{ __('Start Date') }}" value="{{ request('start_date') }}">
                                </div>

                                <div class="table-search position-relative d-print-none">
                                    <input class="form-control" type="date" name="end_date" 
                                        placeholder="{{ __('End Date') }}" value="{{ request('end_date') }}">
                                </div>
                            </div>
                        </form>

                        <div class="table-top-btn-group d-print-none">
                            <ul>
                                <li>
                                    <a onclick="window.print()" class="print-window">
                                        <img src="{{ asset('assets/images/logo/printer.svg') }}" alt="{{ __('Print') }}">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="responsive-table m-0" id="walk-due-table">
                    @include('business::walk-dues.datas')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Auto-submit form when date inputs change
    $('input[name="start_date"], input[name="end_date"]').on('change', function() {
        $('.filter-form').submit();
    });
</script>
@endpush
