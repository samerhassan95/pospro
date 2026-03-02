<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
        <tr>
            <th>{{ __('SL') }}.</th>
            @if(auth()->user()->accessToMultiBranch())
            <th class="text-start">{{ __('Branch') }}</th>
            @endif
            <th class="text-start">{{ __('Amount') }}</th>
            <th class="text-start">{{ __('Category') }}</th>
            <th class="text-start">{{ __('Expense For') }}</th>
            <th class="text-start">{{ __('Payment Type') }}</th>
            <th class="text-start">{{ __('Reference Number') }}</th>
            <th class="text-start">{{ __('Expense Date') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($expense_reports as $expense_report)
                <tr>
                    <td>{{ ($expense_reports->currentPage() - 1) * $expense_reports->perPage() + $loop->iteration }}</td>
                    @if(auth()->user()->accessToMultiBranch())
                    <td class="text-start">{{ $expense_report->branch->name ?? '' }}</td>
                    @endif
                    <td class="text-start">{!! currency_format($expense_report->amount, currency: business_currency()) !!}</td>
                    <td class="text-start">{{ $expense_report->category->categoryName }}</td>
                    <td class="text-start">{{ $expense_report->expanseFor }}</td>
                    <td class="text-start">{{ $expense_report->payment_type_id != null ? $expense_report->payment_type->name ?? '' : $expense_report->paymentType }}</td>
                    <td class="text-start">{{ $expense_report->referenceNo }}</td>
                    <td class="text-start">{{ formatted_date($expense_report->expenseDate) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $expense_reports->links('vendor.pagination.bootstrap-5') }}
</div>
