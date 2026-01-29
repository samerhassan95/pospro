@extends('layouts.business.master')

@section('title')
    {{ __('Domains') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('Domain List') }}</h4>
                        @usercan('domains.create')
                        <a href="#create-domain" class="add-order-btn rounded-2 active" data-bs-toggle="modal"> <i class="fas fa-plus-circle"></i> {{ __('Add New Domain') }}</a>
                        @endusercan
                    </div>
                    <div class="table-top-form p-16">
                        <form action="{{ route('business.domains.filter') }}" method="post" class="filter-form" table="#domains-data">
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

                    <div class="delete-item delete-show d-none delete-popup">
                        <div class="delete-item-show">
                            <p class="fw-bold"><span class="selected-count"></span> {{ __('row selected') }}</p>
                            <button data-bs-toggle="modal" class="trigger-modal" data-bs-target="#multi-delete-modal"
                                data-url="{{ route('business.domains.delete-all') }}">{{ __('Delete') }}</button>
                        </div>
                    </div>


                    <div class="responsive-table">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    @usercan('domains.delete')
                                    <th class="w-60 d-print-none">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="checkbox" class="select-all-delete multi-delete">
                                        </div>
                                    </th>
                                    @endusercan
                                    <th class="min-w-49 ">{{ __('SL') }}.</th>
                                    <th>{{ __('Domain Name') }} </th>
                                    <th>{{ __('Is Verified') }}</th>
                                    <th>{{ __('Has SSL') }}</th>
                                    <th>{{ __('Added on') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="print-d-none">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="domains-data">
                                @include('customdomainaddon::business.datas')
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $domains->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include('customdomainaddon::business.create')
    @include('customdomainaddon::component.delete-modal')
@endpush


