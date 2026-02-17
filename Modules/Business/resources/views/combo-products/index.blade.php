@extends('layouts.business.master')

@section('title')
    {{ __('Combo Product') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Combo Product') }}</h4>
                        <div>
                            <a href="{{ route('business.combo-products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('Add Combo Product') }}
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="responsive-table m-0" id="combo-products-table">
                        @include('business::combo-products.datas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if(session('success'))
@push('scripts')
<script>
    toastr.success('{{ session('success') }}');
</script>
@endpush
@endif

@if(session('error'))
@push('scripts')
<script>
    toastr.error('{{ session('error') }}');
</script>
@endpush
@endif
