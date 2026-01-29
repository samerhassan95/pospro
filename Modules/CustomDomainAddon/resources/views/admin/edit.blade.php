@extends('layouts.master')

@section('title')
    {{__('Edit Domain')}}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card border-0">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{__('Edit Domain') }}</h4>
                        @can('domains-read')
                        <a href="{{ route('admin.domains.index') }}" class="theme-btn print-btn text-light">
                            <i class="fas fa-list" aria-hidden="true"></i>
                            {{ __('View List') }}
                        </a>
                        @endcan
                    </div>
                    <div class="order-form-section p-16">
                        <form action="{{ route('admin.domains.update', $domain->id) }}" method="post" class="ajaxform_instant_reload">
                            @csrf
                            @method('put')

                            <div class="add-suplier-modal-wrapper">
                                <div class="row">
                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Domain Name') }}</label>
                                        <input type="text" name="domain" value="{{ $domain->domain }}" class="form-control">
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <label>{{ __('Is Verified') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="is_verified" required="" class="form-control select-dropdown">
                                                <option value="0" {{ $domain->is_verified == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                <option value="1" {{ $domain->is_verified == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <label>{{ __('Has SSL') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="is_ssl_enabled" required="" class="form-control select-dropdown">
                                                <option value="0" {{ $domain->is_ssl_enabled == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                <option value="1" {{ $domain->is_ssl_enabled == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <label>{{ __('Status') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="status" required="" class="form-control select-dropdown">
                                                <option value="0" {{ $domain->status == 0 ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                <option value="1" {{ $domain->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                <option value="2" {{ $domain->status == 2 ? 'selected' : '' }}>{{ __('Deleted by user') }}</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="button-group text-center mt-5">
                                            <a href="" class="theme-btn border-btn m-2">{{__('Cancel')}}</a>
                                            @can('domains-update')
                                            <button class="theme-btn m-2 submit-btn">{{__('Update')}}</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
