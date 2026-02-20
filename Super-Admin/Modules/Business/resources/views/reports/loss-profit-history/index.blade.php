@extends('layouts.business.master')

@section('title')
    {{ __('Loss Profit History') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ __('Loss Profit History') }}</h4>
                        </div>
                        <div class="col-md-6 text-end">
                             <form action="{{ route('business.loss-profit-history.index') }}" method="get" class="d-inline-block">
                                <div class="form-group d-flex align-items-center">
                                    <label for="duration" class="me-2">{{ __('Filter By Date') }}:</label>
                                    <select name="duration" id="duration" class="form-control select2" onchange="this.form.submit()" style="width: 200px;">
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
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <h5>{{ __('Total Sales') }}</h5>
                            <h3>{{ currency_format($grossSaleProfit, currency: business_currency()) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <h5>{{ __('Gross Income') }}</h5>
                             <small>{{ __('(Sales Profit + Incomes)') }}</small>
                            <h3>{{ currency_format($grossIncomeProfit, currency: business_currency()) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body text-center">
                            <h5>{{ __('Total Expenses') }}</h5>
                            <h3>{{ currency_format($totalExpenses, currency: business_currency()) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                     <div class="card {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} text-white h-100">
                        <div class="card-body text-center">
                            <h5>{{ __('Net Profit') }}</h5>
                            <h3>{{ currency_format($netProfit, currency: business_currency()) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">{{ __('Income Details (Sales + Incomes)') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th class="text-end">{{ __('Amount / Income') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mergedIncomeSaleData as $item)
                                        <tr>
                                            <td>{{ $item->date }}</td>
                                            <td>
                                                <span class="badge {{ $item->type == 'Sale' ? 'bg-primary' : 'bg-success' }}">
                                                    {{ $item->type }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                {{ currency_format($item->total_incomes, currency: business_currency()) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">{{ __('No data found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                         <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">{{ __('Expense Details (Expenses + Payroll)') }}</h5>
                        </div>
                        <div class="card-body p-0">
                             <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th class="text-end">{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mergedExpenseData as $item)
                                        <tr>
                                            <td>{{ $item->date }}</td>
                                            <td>
                                                <span class="badge {{ $item->type == 'Payroll' ? 'bg-warning' : 'bg-danger' }}">
                                                    {{ $item->type }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                {{ currency_format($item->total_expenses, currency: business_currency()) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">{{ __('No data found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
