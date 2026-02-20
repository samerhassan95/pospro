@extends('layouts.business.master')

@section('title')
    {{ __('Product Wise Sale') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Product Wise Sale') }}</h4>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Product Name') }}</th>
                                    <th>{{ __('SKU') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Total Quantity') }}</th>
                                    <th>{{ __('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ $product->total_quantity }}</td>
                                        <td class="fw-bold">{{ number_format($product->total_sales, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No products found') }}</td>
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
