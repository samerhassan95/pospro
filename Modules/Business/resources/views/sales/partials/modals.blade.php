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

<!-- Barcode Scanner Modal -->
<div class="modal fade" id="barcodeScannerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg width="24" height="24" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                        <g clip-path="url(#clip0_456_3729)">
                            <path d="M0 0.625C0 0.45924 0.0691404 0.300269 0.192211 0.183058C0.315282 0.065848 0.482202 0 0.65625 0L4.59375 0C4.7678 0 4.93472 0.065848 5.05779 0.183058C5.18086 0.300269 5.25 0.45924 5.25 0.625C5.25 0.79076 5.18086 0.949732 5.05779 1.06694C4.93472 1.18415 4.7678 1.25 4.59375 1.25H1.3125V4.375C1.3125 4.54076 1.24336 4.69973 1.12029 4.81694C0.997218 4.93415 0.830298 5 0.65625 5C0.482202 5 0.315282 4.93415 0.192211 4.81694C0.0691404 4.69973 0 4.54076 0 4.375V0.625ZM15.75 0.625C15.75 0.45924 15.8191 0.300269 15.9422 0.183058C16.0653 0.065848 16.2322 0 16.4062 0L20.3438 0C20.5178 0 20.6847 0.065848 20.8078 0.183058C20.9309 0.300269 21 0.45924 21 0.625V4.375C21 4.54076 20.9309 4.69973 20.8078 4.81694C20.6847 4.93415 20.5178 5 20.3438 5C20.1697 5 20.0028 4.93415 19.8797 4.81694C19.7566 4.69973 19.6875 4.54076 19.6875 4.375V1.25H16.4062C16.2322 1.25 16.0653 1.18415 15.9422 1.06694C15.8191 0.949732 15.75 0.79076 15.75 0.625ZM0.65625 15C0.830298 15 0.997218 15.0658 1.12029 15.1831C1.24336 15.3003 1.3125 15.4592 1.3125 15.625V18.75H4.59375C4.7678 18.75 4.93472 18.8158 5.05779 18.9331C5.18086 19.0503 5.25 19.2092 5.25 19.375C5.25 19.5408 5.18086 19.6997 5.05779 19.8169C4.93472 19.9342 4.7678 20 4.59375 20H0.65625C0.482202 20 0.315282 19.9342 0.192211 19.8169C0.0691404 19.6997 0 19.5408 0 19.375V15.625C0 15.4592 0.0691404 15.3003 0.192211 15.1831C0.315282 15.0658 0.482202 15 0.65625 15ZM20.3438 15C20.5178 15 20.6847 15.0658 20.8078 15.1831C20.9309 15.3003 21 15.4592 21 15.625V19.375C21 19.5408 20.9309 19.6997 20.8078 19.8169C20.6847 19.9342 20.5178 20 20.3438 20H16.4062C16.2322 20 16.0653 19.9342 15.9422 19.8169C15.8191 19.6997 15.75 19.5408 15.75 19.375C15.75 19.2092 15.8191 19.0503 15.9422 18.9331C16.0653 18.8158 16.2322 18.75 16.4062 18.75H19.6875V15.625C19.6875 15.4592 19.7566 15.3003 19.8797 15.1831C20.0028 15.0658 20.1697 15 20.3438 15Z" fill="#333333"/>
                            <path d="M9.1875 2.5H2.625V8.75H9.1875V2.5ZM3.9375 3.75H7.875V7.5H3.9375V3.75ZM6.5625 13.75H5.25V15H6.5625V13.75Z" fill="black"/>
                            <path d="M9.1875 11.25H2.625V17.5H9.1875V11.25ZM3.9375 12.5H7.875V16.25H3.9375V12.5ZM14.4375 5H15.75V6.25H14.4375V5Z" fill="black"/>
                            <path d="M11.8125 2.5H18.375V8.75H11.8125V2.5ZM13.125 3.75V7.5H17.0625V3.75H13.125ZM10.5 10V12.5H11.8125V13.75H10.5V15H13.125V12.5H14.4375V15H15.75V13.75H18.375V12.5H14.4375V10H10.5ZM13.125 12.5H11.8125V11.25H13.125V12.5ZM18.375 15H17.0625V16.25H14.4375V17.5H18.375V15ZM13.125 17.5V16.25H10.5V17.5H13.125Z" fill="black"/>
                            <path d="M15.75 11.25H18.375V10H15.75V11.25Z" fill="black"/>
                            <ellipse cx="5.89163" cy="5.61111" rx="0.758333" ry="0.722222" fill="black"/>
                            <path d="M0.408203 10L20.7665 9.83334" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_456_3729">
                                <rect width="21" height="20" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                    {{ __('Barcode & QR Code Scanner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Search Section -->
                <div class="barcode-search-section mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="barcode-search-input" placeholder="{{ __('Scan or type product code, name, barcode, or QR code...') }}" autofocus>
                                <button class="btn btn-outline-secondary" type="button" id="clear-search-btn">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" id="start-camera-scan">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                    <path d="M2 6C2 4.89543 2.89543 4 4 4H6L7 2H13L14 4H16C17.1046 4 18 4.89543 18 6V14C18 15.1046 17.1046 16 16 16H4C2.89543 16 2 15.1046 2 14V6Z" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                {{ __('Start Camera') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Camera Section (Hidden by default) -->
                <div id="camera-section" class="mb-4" style="display: none;">
                    <div class="camera-container text-center">
                        <video id="barcode-scanner-video" width="400" height="300" style="border: 2px solid #ddd; border-radius: 8px;"></video>
                        <div class="camera-controls mt-2">
                            <button type="button" class="btn btn-danger" id="stop-camera-scan">
                                {{ __('Stop Camera') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Results Section -->
                <div class="barcode-results-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ __('Search Results') }}</h6>
                        <span class="badge bg-secondary" id="results-count">0 {{ __('products found') }}</span>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="search-loading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Searching products...') }}</p>
                    </div>

                    <!-- No Results State -->
                    <div id="no-results" class="text-center py-4" style="display: none;">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                            <circle cx="32" cy="32" r="30" stroke="#E5E7EB" stroke-width="4"/>
                            <path d="M22 22L42 42M22 42L42 22" stroke="#E5E7EB" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                        <h6 class="text-muted">{{ __('No products found') }}</h6>
                        <p class="text-muted small">{{ __('Try searching with a different code or product name') }}</p>
                    </div>

                    <!-- Results Table -->
                    <div class="table-responsive" id="results-table-container">
                        <table class="table table-hover" id="barcode-results-table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Product Name') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="barcode-results-tbody">
                                <!-- Results will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
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
@include('business::sales.partials.b2b-additional-fields')
