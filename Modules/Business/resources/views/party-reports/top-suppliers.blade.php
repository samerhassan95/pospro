@extends('layouts.business.master')

@section('title')
    {{ __('Top 5 Supplier') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Top 5 Supplier') }}</h4>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('Rank') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Total Purchases') }}</th>
                                    <th>{{ __('Total Transactions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topSuppliers as $key => $supplier)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $key == 0 ? 'warning' : ($key == 1 ? 'secondary' : 'info') }}">
                                                #{{ $key + 1 }}
                                            </span>
                                        </td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->email ?? '-' }}</td>
                                        <td class="fw-bold text-success">{{ number_format($supplier->purchases_sum_total_amount ?? 0, 2) }}</td>
                                        <td>{{ $supplier->purchases_count ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No suppliers found') }}</td>
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
