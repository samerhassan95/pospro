@extends('layouts.business.master')

@section('title')
    {{ __('Edit Commission') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Edit Commission') }} - {{ $user->name }}</h4>
                        <a href="{{ route('business.commissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>

                    <form id="commission-form" action="{{ route('business.commissions.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row p-16">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('User') }}</label>
                                    <input type="text" class="form-control" value="{{ $user->name }} ({{ $user->email }})" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="commission_type">{{ __('Commission Type') }} <span class="text-danger">*</span></label>
                                    <select name="commission_type" id="commission_type" class="form-control" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="percentage" {{ $user->commission_type == 'percentage' ? 'selected' : '' }}>
                                            {{ __('Percentage') }} (%)
                                        </option>
                                        <option value="fixed" {{ $user->commission_type == 'fixed' ? 'selected' : '' }}>
                                            {{ __('Fixed Amount') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="commission_value">{{ __('Commission Value') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="commission_value" id="commission_value" 
                                           class="form-control" step="0.01" min="0" required
                                           value="{{ $user->commission_value }}"
                                           placeholder="{{ __('Enter commission value') }}">
                                    <small class="text-muted" id="commission-hint"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Update Commission') }}
                                </button>
                                <button type="button" class="btn btn-danger" onclick="removeCommission()">
                                    <i class="fas fa-trash"></i> {{ __('Remove Commission') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('commission_type').addEventListener('change', function() {
            const hint = document.getElementById('commission-hint');
            if (this.value === 'percentage') {
                hint.textContent = '{{ __("Enter percentage value (e.g., 5 for 5%)") }}';
            } else if (this.value === 'fixed') {
                hint.textContent = '{{ __("Enter fixed amount") }}';
            } else {
                hint.textContent = '';
            }
        });

        // Trigger on page load
        document.getElementById('commission_type').dispatchEvent(new Event('change'));

        function removeCommission() {
            if (confirm('{{ __("Are you sure you want to remove this commission?") }}')) {
                fetch('{{ route("business.commissions.destroy", $user->id) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                });
            }
        }
    </script>
@endsection
