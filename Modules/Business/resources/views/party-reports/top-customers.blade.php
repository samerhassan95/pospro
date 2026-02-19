@extends('layouts.business.master')

@section('title')
    {{ __('Top 5 Customer') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('Top 5 Customer') }}</h4>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="add-order-btn rounded-2">
                                <i class="fas fa-print me-1"></i> {{ __('Print') }}
                            </button>
                            <a href="{{ route('business.parties.index', ['type' => 'Customer']) }}" class="add-order-btn rounded-2">
                                <i class="fas fa-users me-1"></i> {{ __('All Customers') }}
                            </a>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Rank') }}</th>
                                    <th>{{ __('Customer Details') }}</th>
                                    <th>{{ __('Contact Info') }}</th>
                                    <th>{{ __('Total Sales') }}</th>
                                    <th>{{ __('Total Transactions') }}</th>
                                    <th>{{ __('Average Order Value') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topCustomers as $key => $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($key == 0)
                                                    <i class="fas fa-trophy text-warning me-2"></i>
                                                @elseif($key == 1)
                                                    <i class="fas fa-medal text-secondary me-2"></i>
                                                @elseif($key == 2)
                                                    <i class="fas fa-award text-warning me-2" style="color: #cd7f32 !important;"></i>
                                                @else
                                                    <i class="fas fa-star text-info me-2"></i>
                                                @endif
                                                <span class="badge bg-{{ $key == 0 ? 'warning' : ($key == 1 ? 'secondary' : ($key == 2 ? 'info' : 'light')) }} text-{{ $key < 3 ? 'dark' : 'dark' }}">
                                                    #{{ $key + 1 }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $customer->name }}</h6>
                                                    <small class="text-muted">{{ __('Customer ID') }}: #{{ $customer->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="mb-1">
                                                    <i class="fas fa-phone text-muted me-1"></i>
                                                    <span>{{ $customer->phone }}</span>
                                                </div>
                                                <div>
                                                    <i class="fas fa-envelope text-muted me-1"></i>
                                                    <span>{{ $customer->email ?? __('N/A') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-6">
                                                {{ currency_format($customer->sales_sum_total_amount ?? 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $customer->sales_count ?? 0 }} {{ __('Orders') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $avgOrderValue = ($customer->sales_count ?? 0) > 0 
                                                    ? ($customer->sales_sum_total_amount ?? 0) / ($customer->sales_count ?? 1) 
                                                    : 0;
                                            @endphp
                                            <span class="text-primary fw-bold">{{ currency_format($avgOrderValue) }}</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('business.customer-ledger.show', $customer->id) }}">
                                                        <i class="fas fa-book me-2"></i>{{ __('View Ledger') }}
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('business.parties.edit', $customer->id) }}">
                                                        <i class="fas fa-eye me-2"></i>{{ __('View Details') }}
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('business.sales.create', ['customer_id' => $customer->id]) }}">
                                                        <i class="fas fa-plus me-2"></i>{{ __('New Sale') }}
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">{{ __('No customers found') }}</h5>
                                                <p class="text-muted">{{ __('No customer sales data available yet. Start making sales to see top customers.') }}</p>
                                                <a href="{{ route('business.parties.create', ['type' => 'Customer']) }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>{{ __('Add Customer') }}
                                                </a>
                                            </div>
                                        </td>
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
