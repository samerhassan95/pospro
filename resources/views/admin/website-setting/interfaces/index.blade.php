@extends('layouts.master')

@section('title')
    {{ __('Interfaces List') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-bodys ">
                    <div class="table-header p-16">
                        <h4>{{ __('Interfaces List') }}</h4>
                        <a href="{{ route('admin.interfaces.create') }}" class="theme-btn print-btn text-light">
                            <i class="far fa-plus" aria-hidden="true"></i>
                            {{ __('Create New') }}
                        </a>
                    </div>

                    <div class="table-top-form p-16-0">
                        <form action="{{ route('admin.interfaces.index') }}" method="GET" class="filter-form" table="#interfaces-data">
                            <div class="table-top-left d-flex gap-3 margin-l-16">
                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="per_page" class="form-control">
                                        <option value="5" @selected(request('per_page') == 5 || !request('per_page'))>{{ __('Show- 5') }}</option>
                                        <option value="10" @selected(request('per_page') == 10)>{{ __('Show- 10') }}</option>
                                        <option value="25" @selected(request('per_page') == 25)>{{ __('Show- 25') }}</option>
                                        <option value="50" @selected(request('per_page') == 50)>{{ __('Show- 50') }}</option>
                                        <option value="100" @selected(request('per_page') == 100)>{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="interfaces-data">
                    @include('admin.website-setting.interfaces.datas')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include('admin.components.multi-delete-modal')
@endpush
