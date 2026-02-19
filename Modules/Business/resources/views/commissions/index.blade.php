@extends('layouts.business.master')

@section('title')
    {{ __('Commission Management') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16 d-print-none">
                        <h4>{{ __('Commission Management') }}</h4>
                        @usercan('commissions.create')
                        <a href="{{ route('business.commissions.create') }}" class="add-order-btn rounded-2">
                            <i class="fas fa-plus-circle me-1"></i>{{ __('Set Commission') }}
                        </a>
                        @endusercan
                    </div>

                    <div class="table-header justify-content-center border-0 text-center d-none d-block d-print-block">
                        @include('business::print.header')
                        <h4 class="mt-2">{{ __('Commission Management') }}</h4>
                    </div>

                    <div class="table-top-form p-16">
                        <form action="{{ route('business.commissions.filter') }}" method="post" class="filter-form" table="#commission-data">
                            @csrf
                            <div class="table-top-left d-flex gap-3">
                                <div class="gpt-up-down-arrow position-relative d-print-none">
                                    <select name="per_page" class="form-control">
                                        <option value="5" selected>{{ __('Show- 5') }}</option>
                                        <option value="10">{{ __('Show- 10') }}</option>
                                        <option value="25">{{ __('Show- 25') }}</option>
                                        <option value="50">{{ __('Show- 50') }}</option>
                                        <option value="100">{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>

                                <div class="table-search position-relative d-print-none">
                                    <input class="form-control searchInput" type="text" name="search"
                                        placeholder="{{ __('Search by name or email...') }}" value="{{ request('search') }}">
                                    <span class="position-absolute">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
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

                <div class="responsive-table m-0" id="commission-data">
                    @include('business::commissions.datas')
                </div>
            </div>
        </div>
    </div>
@endsection
