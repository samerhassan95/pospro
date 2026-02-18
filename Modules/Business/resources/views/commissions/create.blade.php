@extends('layouts.business.master')

@section('title')
    {{ __('Set Commission') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Set Commission') }}</h4>
                        <a href="{{ route('business.commissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>

                    <form id="commission-form" action="{{ route('business.commissions.store') }}" method="POST">
                        @csrf
                        <div class="row p-16">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_id">{{ __('Select User') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="">{{ __('Select User') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="commission_type">{{ __('Commission Type') }} <span class="text-danger">*</span></label>
                                    <select name="commission_type" id="commission_type" class="form-control" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="percentage">{{ __('Percentage') }} (%)</option>
                                        <option value="fixed">{{ __('Fixed Amount') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="commission_value">{{ __('Commission Value') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="commission_value" id="commission_value" 
                                           class="form-control" step="0.01" min="0" required
                                           placeholder="{{ __('Enter commission value') }}">
                                    <small class="text-muted" id="commission-hint"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Save Commission') }}
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
    </script>
@endsection
