@extends('layouts.business.master')

@section('title')
    {{ __('Top 5 Products') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Top 5 Products') }}</h4>
                    </div>

                    <div class="row mb-3 p-16">
                        <div class="col-md-6 offset-md-6">
                            <form action="{{ route('business.top-products.index') }}" method="get">
                                <div class="form-group">
                                    <label for="duration">{{ __('Filter By Date') }}</label>
                                    <select name="duration" id="duration" class="form-control select2" onchange="this.form.submit()">
                                        <option value="today" {{ request('duration') == 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                                        <option value="yesterday" {{ request('duration') == 'yesterday' ? 'selected' : '' }}>{{ __('Yesterday') }}</option>
                                        <option value="last_seven_days" {{ request('duration') == 'last_seven_days' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                                        <option value="current_month" {{ request('duration') == 'current_month' ? 'selected' : '' }}>{{ __('Current Month') }}</option>
                                        <option value="last_month" {{ request('duration') == 'last_month' ? 'selected' : '' }}>{{ __('Last Month') }}</option>
                                        <option value="current_year" {{ request('duration') == 'current_year' ? 'selected' : '' }}>{{ __('Current Year') }}</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered text-center h-100">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Product Name') }}</th>
                                    <th>{{ __('Product Code') }}</th>
                                    <th>{{ __('Total Quantity Sold') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <img src="{{ asset($product->image ?? 'assets/images/logo/upload2.jpg') }}" alt="product" class="table-product-img">
                                        </td>
                                        <td>{{ $product->productName }}</td>
                                        <td>{{ $product->productCode }}</td>
                                        <td class="fw-bold text-primary">{{ $product->sale_details_sum_quantities ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('No products found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
