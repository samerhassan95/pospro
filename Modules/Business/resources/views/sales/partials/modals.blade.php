<!-- POS Modals -->

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Table') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('Table Number') }}</label>
                    <input type="text" class="form-control" id="new-table-name" placeholder="Ta11, Ta12, etc.">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Number of Chairs') }}</label>
                    <select class="form-select" id="new-table-chairs">
                        <option value="2">2 {{ __('Chairs') }}</option>
                        <option value="4" selected>4 {{ __('Chairs') }}</option>
                        <option value="6">6 {{ __('Chairs') }}</option>
                        <option value="8">8 {{ __('Chairs') }}</option>
                        <option value="10">10 {{ __('Chairs') }}</option>
                        <option value="12">12 {{ __('Chairs') }}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="save-new-table">{{ __('Add Table') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Make Reservation Modal -->
<div class="modal fade" id="makeReservationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Make Reservation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Customer Name') }}</label>
                        <input type="text" class="form-control" id="reservation-customer-name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Phone Number') }}</label>
                        <input type="tel" class="form-control" id="reservation-phone">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="date" class="form-control" id="reservation-date" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Time') }}</label>
                        <input type="time" class="form-control" id="reservation-time" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Number of Guests') }}</label>
                        <input type="number" class="form-control" id="reservation-guests" value="2" min="1" max="20" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Special Notes') }}</label>
                    <textarea class="form-control" id="reservation-notes" rows="2"></textarea>
                </div>
                <div class="mb-3 flex justify-end">
                    <button type="button" class="btn btn-primary" id="search-available-tables">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                            <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        {{ __('Search Available Tables') }}
                    </button>
                </div>
                <div id="available-tables-list" style="display: none;">
                    <h6 class="mb-3">{{ __('Available Tables') }}:</h6>
                    <div id="available-tables-container" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-reservation" disabled>{{ __('Confirm Reservation') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Reservations Modal -->
<div class="modal fade" id="manageReservationsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Manage Reservations') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Table') }}</th>
                                <th>{{ __('Customer Name') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Guests') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="reservations-table-body"></tbody>
                    </table>
                </div>
                <div id="no-reservations-message" style="display: none; text-align: center; padding: 40px;">
                    <p class="text-muted">{{ __('No reservations found') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Orders Modal -->
<div class="modal fade" id="manageOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Manage Orders') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Table') }}</th>
                                <th>{{ __('Customer Name') }}</th>
                                <th>{{ __('Guests') }}</th>
                                <th>{{ __('Order Items') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th>{{ __('Started At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body"></tbody>
                    </table>
                </div>
                <div id="no-orders-message" style="display: none; text-align: center; padding: 40px;">
                    <p class="text-muted">{{ __('No active orders found') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage All Tables Modal -->
<div class="modal fade" id="manageAllTablesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Manage All Tables') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Table Name') }}</th>
                                <th>{{ __('Chairs') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Current Order/Reservation') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="all-tables-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Details Modal -->
<div class="modal fade" id="reservationDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Reservation Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>{{ __('Table') }}:</strong>
                    <span id="detail-table" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Customer Name') }}:</strong>
                    <span id="detail-customer" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Phone') }}:</strong>
                    <span id="detail-phone" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Date') }}:</strong>
                    <span id="detail-date" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Time') }}:</strong>
                    <span id="detail-time" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Number of Guests') }}:</strong>
                    <span id="detail-guests" class="ms-2"></span>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Special Notes') }}:</strong>
                    <p id="detail-notes" class="mt-2 p-2 bg-light rounded"></p>
                </div>
                <div class="mb-3">
                    <strong>{{ __('Status') }}:</strong>
                    <span id="detail-status" class="ms-2"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="cancel-reservation-btn">{{ __('Cancel Reservation') }}</button>
                <button type="button" class="btn btn-primary" id="guest-arrived-btn">{{ __('Guest Arrived') }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Table Order Modal -->
<div class="modal fade" id="tableOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Table Order') }} - <span id="order-table-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-order-info">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Customer Name') }}</label>
                            <input type="text" class="form-control" id="order-customer-name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Number of Guests') }}</label>
                            <input type="number" class="form-control" id="order-guests" value="1" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Order Items') }}</label>
                        <textarea class="form-control" id="order-items" rows="4" placeholder="{{ __('Enter order items...') }}"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Special Notes') }}</label>
                        <textarea class="form-control" id="order-notes" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('Order Time') }}</label>
                            <input type="time" class="form-control" id="order-time" value="{{ now()->format('H:i') }}">
                        </div>
                    </div>
                    <input type="hidden" id="order-table-status" value="utilized">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="save-table-order">{{ __('Save Order') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Order Modal -->
<div class="modal fade" id="completeOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #10b981; color: white;">
                <h5 class="modal-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="vertical-align: middle; margin-right: 8px;">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Complete Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="complete-order-info">
                    <h6 style="font-weight: bold; margin-bottom: 15px;">{{ __('Order Details') }}</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 40%; color: #666;">{{ __('Table') }}:</td>
                            <td style="font-weight: bold;" id="complete-table-name"></td>
                        </tr>
                        <tr>
                            <td style="color: #666;">{{ __('Customer') }}:</td>
                            <td id="complete-customer-name"></td>
                        </tr>
                        <tr>
                            <td style="color: #666;">{{ __('Guests') }}:</td>
                            <td id="complete-guests"></td>
                        </tr>
                        <tr>
                            <td style="color: #666;">{{ __('Order Time') }}:</td>
                            <td id="complete-order-time"></td>
                        </tr>
                    </table>

                    <div style="background: #f3f4f6; padding: 12px; border-radius: 8px; margin-top: 15px;">
                        <strong>{{ __('Order Items') }}:</strong>
                        <div id="complete-order-items" style="margin-top: 8px; white-space: pre-wrap;"></div>
                    </div>

                    <div id="complete-notes-section" style="margin-top: 15px; display: none;">
                        <strong>{{ __('Notes') }}:</strong>
                        <div id="complete-order-notes" style="margin-top: 8px; color: #666; font-style: italic;"></div>
                    </div>

                    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin-top: 20px; border-radius: 4px;">
                        <strong style="color: #92400e;">⚠️ {{ __('Confirm Completion') }}</strong>
                        <p style="margin: 8px 0 0 0; color: #78350f; font-size: 14px;">
                            {{ __('This will mark the order as complete and free the table for new customers.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-success" id="confirm-complete-order">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align: middle; margin-right: 6px;">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Complete Order') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include existing modals -->
@include('business::sales.calculator')
@include('business::sales.category-search')
@include('business::sales.brand-search')
@include('business::sales.customer-create')
@include('business::sales.stock-list')
