@extends('layouts.business.master')

@section('title')
    {{ __('Party Ledger') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ $party->name }} {{ __('Ledger') }}</h4>
                        <a href="{{ route('business.parties.index', ['type' => $party->type == 'Supplier' ? 'Supplier' : 'Customer']) }}" class="btn btn-secondary rounded-2">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                        </a>
                    </div>
                   
                    <div class="row mb-3 p-16">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $party->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $party->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <td>{{ $party->type }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $party->address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                             <form action="{{ route('business.parties.ledger', $party->id) }}" method="get" class="filter-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="duration">{{ __('Filter By Date') }}</label>
                                            <select name="duration" id="duration" class="form-control select2" onchange="this.form.submit()">
                                                <option value="">{{ __('All Time') }}</option>
                                                <option value="today" {{ request('duration') == 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                                                <option value="yesterday" {{ request('duration') == 'yesterday' ? 'selected' : '' }}>{{ __('Yesterday') }}</option>
                                                <option value="last_seven_days" {{ request('duration') == 'last_seven_days' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                                                <option value="current_month" {{ request('duration') == 'current_month' ? 'selected' : '' }}>{{ __('Current Month') }}</option>
                                                <option value="last_month" {{ request('duration') == 'last_month' ? 'selected' : '' }}>{{ __('Last Month') }}</option>
                                                <option value="current_year" {{ request('duration') == 'current_year' ? 'selected' : '' }}>{{ __('Current Year') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Debit') }}</th>
                                    <th>{{ __('Credit') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = 0;
                                @endphp
                                @forelse ($ledger as $item)
                                    @php
                                        // Since the service might return filtered results, calculation logic might need adjustment if service is buggy.
                                        // But assuming service returns correct data:
                                        $debit = $item['debit_amount'] ?? 0;
                                        $credit = $item['credit_amount'] ?? 0;
                                        $balance = $item['balance'] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $item['platform'] ?? '-' }}</td>
                                        <td>{{ $item['invoice_no'] ?? '-' }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($debit, 2) }}</td>
                                        <td class="text-success fw-bold">{{ number_format($credit, 2) }}</td>
                                        <td class="fw-bold">{{ number_format($balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No transactions found') }}</td>
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
