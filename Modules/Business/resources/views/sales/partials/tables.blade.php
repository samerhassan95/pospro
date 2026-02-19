<!-- Tables Section -->
<div class="pos-view-section" id="tables-view">
    <!-- Management Buttons -->
    <div class="table-management-buttons">
        <button type="button" class="btn-primary" id="btn-add-table">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            {{ __('Add Table') }}
        </button>
        <button type="button" class="btn btn-primary" id="btn-clear-all-data" style="background: #374151; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                <path d="M3 3H8V8H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 3H17V8H12V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3 12H8V17H3V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 12H17V17H12V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ __('Manage Tables') }}
        </button>
        <button type="button" class="btn-primary" id="btn-make-reservation">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            {{ __('Make Reservation') }}
        </button>
        <button type="button" class="btn-primary" id="btn-manage-all-tables">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 3H8V8H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 3H17V8H12V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3 12H8V17H3V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 12H17V17H12V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ __('Manage Reservations') }}
        </button>
        <button type="button" class="btn-primary" id="btn-manage-orders" style="background: #10b981; border-color: #10b981;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 3H17V17H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 7H13M7 10H13M7 13H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            {{ __('Manage Orders') }}
        </button>
    </div>

    <!-- Legend -->
    <div class="table-legend">
        <div class="legend-item">
            <span class="legend-color utilized"></span>
            <span class="legend-text">{{ __('Utilized table/chair with guests') }}</span>
        </div>
        <div class="legend-item">
            <span class="legend-color free"></span>
            <span class="legend-text">{{ __('Free table/chair') }}</span>
        </div>
        <div class="legend-item">
            <span class="legend-color blocked"></span>
            <span class="legend-text">{{ __('Blocked table/chair') }}</span>
        </div>
    </div>

    <!-- Restaurant Floor Plan -->
    <div class="floor-plan-wrapper">
        <div class="restaurant-floor-plan" id="restaurant-floor-plan">
            <!-- Entrance -->
            <div class="entrance-area" data-area="entrance" data-entrance-side="right" style="top: 47%; right: 20px;">
                <span class="entrance-label">{{ __('Entrance') }}</span>
                <div class="entrance-arrow"></div>
            </div>

            <!-- Bar Area -->
            <div class="floor-area bar-area" data-area="bar-area">
                <span class="area-label">{{ __('Bar Area') }}</span>
            </div>

            <!-- Toilets Wall -->
            <div class="floor-area toilets-wall" data-area="toilets">
                <span class="toilets-label">{{ __('Toilets') }}</span>
            </div>

            <!-- Center Square -->
            <div class="center-square" data-area="center-square"></div>
            
            <!-- Tables will be dynamically created here by JavaScript -->
        </div>
        <div class="entrance-cutout-cover"></div>
    </div>

    <!-- Table Controls -->
    <div class="table-controls">
        <div class="controls-section">
            <h4 class="controls-title">{{ __('Live Views') }}</h4>
            <div class="toggle-group">
                <label class="toggle-item">
                    <input type="checkbox" class="toggle-input" id="show-utilization" checked>
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">{{ __('Show utilization') }}</span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" class="toggle-input" id="show-ordered">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">{{ __('Show ordered') }}</span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" class="toggle-input" id="show-recommendations">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">{{ __('Show recommendations') }}</span>
                </label>
            </div>
        </div>
        <div class="controls-section">
            <h4 class="controls-title">{{ __('Integration') }}</h4>
            <div class="toggle-group">
                <label class="toggle-item">
                    <input type="checkbox" class="toggle-input" id="show-reservations">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">{{ __('Show reservations') }}</span>
                </label>
            </div>
        </div>
    </div>
</div>
