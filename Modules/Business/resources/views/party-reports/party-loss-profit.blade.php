@extends('layouts.business.master')

@section('title')
    {{ __('Party Profit & Loss') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('Party Profit & Loss') }}</h4>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="add-order-btn rounded-2">
                                <i class="fas fa-print me-1"></i> {{ __('Print') }}
                            </button>
                            <div class="search-box">
                                <input type="text" id="searchInput" class="form-control" placeholder="{{ __('Search parties...') }}">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table" id="partiesTable">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Total Sales') }}</th>
                                    <th>{{ __('Total Purchases') }}</th>
                                    <th>{{ __('Profit/Loss') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($parties as $key => $party)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title bg-primary rounded-circle">
                                                        {{ strtoupper(substr($party->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $party->name }}</h6>
                                                    <small class="text-muted">{{ $party->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $party->type == 'Customer' ? 'bg-success' : 'bg-info' }}">
                                                {{ __($party->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $party->phone }}</td>
                                        <td class="text-success fw-bold">{{ currency_format($party->total_sales ?? 0) }}</td>
                                        <td class="text-primary fw-bold">{{ currency_format($party->total_purchases ?? 0) }}</td>
                                        <td class="{{ ($party->profit_loss ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            <i class="fas fa-{{ ($party->profit_loss ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                            {{ currency_format($party->profit_loss ?? 0) }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($party->type == 'Customer')
                                                        <li><a class="dropdown-item" href="{{ route('business.customer-ledger.show', $party->id) }}">
                                                            <i class="fas fa-book me-2"></i>{{ __('View Ledger') }}
                                                        </a></li>
                                                    @else
                                                        <li><a class="dropdown-item" href="{{ route('business.supplier-ledger.show', $party->id) }}">
                                                            <i class="fas fa-book me-2"></i>{{ __('View Ledger') }}
                                                        </a></li>
                                                    @endif
                                                    <li><a class="dropdown-item" href="{{ route('business.parties.edit', $party->id) }}">
                                                        <i class="fas fa-eye me-2"></i>{{ __('View Details') }}
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">{{ __('No parties found') }}</h5>
                                                <p class="text-muted">{{ __('No party profit/loss data available for the selected period.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchInput');
                    const table = document.getElementById('partiesTable');
                    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                    searchInput.addEventListener('keyup', function() {
                        const filter = this.value.toLowerCase();
                        
                        for (let i = 0; i < rows.length; i++) {
                            const row = rows[i];
                            const name = row.cells[1].textContent.toLowerCase();
                            const phone = row.cells[3].textContent.toLowerCase();
                            const type = row.cells[2].textContent.toLowerCase();
                            
                            if (name.includes(filter) || phone.includes(filter) || type.includes(filter)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                });
                </script>
            </div>
        </div>
    </div>
@endsection
