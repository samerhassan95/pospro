@extends('layouts.master')

@section('title')
    {{ __('Domains List') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16 d-print-none">
                        <h4>{{ __('Domain List') }}</h4>
                        <a href="{{ route('admin.domains.instructions') }}" class="add-order-btn rounded-2"><i class="fas fa-info-circle"></i> {{ __('Get instruction') }} </a>
                    </div>
                    <div class="table-top-form p-16">
                        <form action="{{ route('admin.domains.filter') }}" method="post" class="filter-form" table="#domains-data">
                            @csrf

                            <div class="table-top-left d-flex gap-3">
                                <div class="gpt-up-down-arrow position-relative d-print-none">
                                    <select name="per_page" class="form-control">
                                        <option value="10">{{ __('Show- 10') }}</option>
                                        <option value="25">{{ __('Show- 25') }}</option>
                                        <option value="50">{{ __('Show- 50') }}</option>
                                        <option value="100">{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>

                                <div class="table-search position-relative d-print-none">
                                        <input class="form-control searchInput" type="text" name="search" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
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

                    <div class="responsive-table m-0">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="d-flex align-items-center gap-1">
                                            <label class="table-custom-checkbox">
                                                <input type="checkbox" class="table-hidden-checkbox selectAllCheckbox">
                                                <span class="table-custom-checkmark custom-checkmark"></span>
                                            </label>
                                            <i class="fal fa-trash-alt delete-selected"></i>
                                        </div>
                                    </th>
                                    <th > {{ __('SL') }}.</th>
                                    <th >{{ __('Domain Name') }}</th>
                                    <th >{{ __('Is Verified')}}</th>
                                    <th >{{ __('Has SSL') }}</th>
                                    <th >{{ __('Status') }}</th>
                                    <th >{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="domains-data">
                                @include('customdomainaddon::admin.datas')
                            </tbody>
                        </table>
                    </div>
                </div>

                <nav>
                    <ul class="pagination">
                        <li class="page-item">{{ $domains->links('pagination::bootstrap-5') }}</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include('customdomainaddon::component.delete-modal')
    @include('customdomainaddon::admin.approved')
    @include('customdomainaddon::admin.reject')
@endpush
