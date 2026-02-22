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
                        <div>
                            <h4>{{ __('Party Profit & Loss') }}</h4>
                            <small class="text-muted d-block">{{ __('View profit and loss analysis for all parties.') }}</small>
                        </div>
                        <button onclick="window.print()" class="add-order-btn rounded-2">
                            <i class="fas fa-print me-1"></i> {{ __('Print') }}
                        </button>
                    </div>

                    <div class="table-top-form p-16-0">
                        <div class="table-top-left d-flex gap-3 margin-l-16">
                            <div class="gpt-up-down-arrow position-relative">
                                <select id="perPageSelect" class="form-control">
                                    <option value="10" selected>{{ __('Show- 10') }}</option>
                                    <option value="25">{{ __('Show- 25') }}</option>
                                    <option value="50">{{ __('Show- 50') }}</option>
                                    <option value="100">{{ __('Show- 100') }}</option>
                                </select>
                                <span></span>
                            </div>
                            <div class="table-search position-relative">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="{{ __('Search...') }}">
                                <span class="position-absolute">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                    </svg>
                                </span>
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
                                        <td class="d-print-none">
                                            <div class="dropdown table-action">
                                                <button type="button" data-bs-toggle="dropdown">
                                                    <i class="far fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($party->type == 'Customer')
                                                        <li><a class="dropdown-item" href="{{ route('business.customer-ledger.show', $party->id) }}">
                                                            <i class="fal fa-book"></i>{{ __('View Ledger') }}
                                                        </a></li>
                                                    @else
                                                        <li><a class="dropdown-item" href="{{ route('business.supplier-ledger.show', $party->id) }}">
                                                            <i class="fal fa-book"></i>{{ __('View Ledger') }}
                                                        </a></li>
                                                    @endif
                                                    <li><a class="dropdown-item" href="{{ route('business.parties.edit', $party->id) }}">
                                                        <i class="fal fa-eye"></i>{{ __('View Details') }}
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
                    const perPageSelect = document.getElementById('perPageSelect');
                    const table = document.getElementById('partiesTable');
                    const tbody = table.getElementsByTagName('tbody')[0];
                    const rows = Array.from(tbody.getElementsByTagName('tr'));
                    let currentPage = 1;
                    let itemsPerPage = 10;

                    // Search functionality
                    searchInput.addEventListener('keyup', function() {
                        const filter = this.value.toLowerCase();
                        
                        rows.forEach(row => {
                            if (row.cells.length > 1) { // Skip empty state row
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
                        
                        updatePagination();
                    });

                    // Per page functionality
                    perPageSelect.addEventListener('change', function() {
                        itemsPerPage = parseInt(this.value);
                        currentPage = 1;
                        updatePagination();
                    });

                    function updatePagination() {
                        const visibleRows = rows.filter(row => 
                            row.style.display !== 'none' && row.cells.length > 1
                        );
                        
                        const totalItems = visibleRows.length;
                        const totalPages = Math.ceil(totalItems / itemsPerPage);
                        
                        // Hide all rows first
                        visibleRows.forEach(row => row.style.display = 'none');
                        
                        // Show rows for current page
                        const startIndex = (currentPage - 1) * itemsPerPage;
                        const endIndex = startIndex + itemsPerPage;
                        
                        visibleRows.slice(startIndex, endIndex).forEach(row => {
                            row.style.display = '';
                        });
                        
                        // Update row numbers
                        visibleRows.slice(startIndex, endIndex).forEach((row, index) => {
                            if (row.cells[0]) {
                                row.cells[0].textContent = startIndex + index + 1;
                            }
                        });
                    }

                    // Initialize pagination
                    updatePagination();
                });
                </script>
            </div>
        </div>
    </div>
@endsection
