@extends('business::layouts.app')

@section('title', __('Moyasar Settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Moyasar Payment Settings') }}</h4>
                </div>
                
                <div class="card-body">
                    @if(!$moyasarEnabled)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ __('Moyasar payment gateway is not enabled by the administrator. Please contact support.') }}
                        </div>
                    @else
                        <form action="{{ route('business.moyasar.update') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Environment') }} <span class="text-danger">*</span></label>
                                        <select name="environment" class="form-control" required>
                                            <option value="test" {{ ($moyasar_setting['environment'] ?? 'test') == 'test' ? 'selected' : '' }}>
                                                {{ __('Test Environment') }}
                                            </option>
                                            <option value="live" {{ ($moyasar_setting['environment'] ?? '') == 'live' ? 'selected' : '' }}>
                                                {{ __('Live Environment') }}
                                            </option>
                                        </select>
                                        <small class="text-muted">
                                            {{ __('Select Test for development and Live for production payments') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                {{ __('API keys are managed by the system administrator. You only need to select the environment.') }}
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Save Settings') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection