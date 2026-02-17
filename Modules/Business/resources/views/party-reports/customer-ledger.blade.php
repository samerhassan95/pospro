@extends('layouts.business.master')

@section('title')
    {{ __('Customer Ledger') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Customer Ledger') }}</h4>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Total Sales') }}</th>
                                    <th>{{ __('Total Due') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($parties as $key => $party)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $party->name }}</td>
                                        <td>{{ $party->phone }}</td>
                                        <td>{{ $party->email ?? '-' }}</td>
                                        <td>{{ number_format($party->sales->sum('totalAmount'), 2) }}</td>
                                        <td>{{ number_format($party->sales->sum('dueAmount'), 2) }}</td>
                                        <td>
                                            <a href="{{ route('business.customer-ledger.show', $party->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> {{ __('View Ledger') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No customers found') }}</td>
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
