@extends('layouts.business.master')

@section('title')
    {{ __('Party Profit & Loss') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Party Profit & Loss') }}</h4>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Total Sales') }}</th>
                                    <th>{{ __('Total Purchases') }}</th>
                                    <th>{{ __('Profit/Loss') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($parties as $key => $party)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $party->name }}</td>
                                        <td>
                                            <span class="badge {{ $party->type == 'Customer' ? 'bg-success' : 'bg-info' }}">
                                                {{ $party->type }}
                                            </span>
                                        </td>
                                        <td>{{ $party->phone }}</td>
                                        <td>{{ number_format($party->total_sales, 2) }}</td>
                                        <td>{{ number_format($party->total_purchases, 2) }}</td>
                                        <td class="{{ $party->profit_loss >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ number_format($party->profit_loss, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No parties found') }}</td>
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
