@extends('layouts.app')

@section('title', __('Moyasar Settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Moyasar Payment Gateway Settings') }}</h4>
                    <p class="text-muted">{{ __('Configure global Moyasar settings for all businesses') }}</p>
                </div>
                
                <div class="card-body">
                    <form id="moyasar-settings-form">
                        @csrf
                        
                        <!-- Test Environment -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">{{ __('Test Environment') }}</h5>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Test Publishable Key') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="test_publishable_key" class="form-control" 
                                           value="{{ $settings['test_publishable_key'] ?? '' }}" 
                                           placeholder="pk_test_...">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Test Secret Key') }} <span class="text-danger">*</span></label>
                                    <input type="password" name="test_secret_key" class="form-control" 
                                           placeholder="sk_test_...">
                                </div>
                            </div>
                        </div>

                        <!-- Live Environment -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-success">{{ __('Live Environment') }}</h5>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Live Publishable Key') }}</label>
                                    <input type="text" name="live_publishable_key" class="form-control" 
                                           value="{{ $settings['live_publishable_key'] ?? '' }}" 
                                           placeholder="pk_live_...">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Live Secret Key') }}</label>
                                    <input type="password" name="live_secret_key" class="form-control" 
                                           placeholder="sk_live_...">
                                </div>
                            </div>
                        </div>

                        <!-- General Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>{{ __('General Settings') }}</h5>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Default Currency') }} <span class="text-danger">*</span></label>
                                    <select name="default_currency" class="form-control">
                                        <option value="SAR" {{ ($settings['default_currency'] ?? '') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                        <option value="USD" {{ ($settings['default_currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ ($settings['default_currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="AED" {{ ($settings['default_currency'] ?? '') == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Commission Rate') }} (%)</label>
                                    <input type="number" name="commission_rate" class="form-control" 
                                           value="{{ $settings['commission_rate'] ?? '0' }}" 
                                           min="0" max="100" step="0.01">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Webhook Secret') }}</label>
                                    <input type="password" name="webhook_secret" class="form-control" 
                                           placeholder="whsec_...">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" 
                                           {{ ($settings['is_active'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ __('Enable Moyasar for all businesses') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Save Settings') }}
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="testConnection()">
                                <i class="fas fa-plug"></i> {{ __('Test Connection') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('moyasar-settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("super-admin.moyasar.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("Something went wrong.") }}');
    });
});

function testConnection() {
    alert('{{ __("Testing connection...") }}');
}
</script>
@endsection