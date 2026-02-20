@extends('layouts.business.master')

@section('title')
    {{ __('Add Combo Product') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Add Combo Product') }}</h4>
                        <a href="{{ route('business.combo-products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>

                    <form action="{{ route('business.combo-products.store') }}" method="POST">
                        @csrf
                        <div class="row p-16">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="product_id">{{ __('Select Product') }} <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->productName }} ({{ $product->productCode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="stock_id">{{ __('Select Stock') }} <span class="text-danger">*</span></label>
                                    <select name="stock_id" id="stock_id" class="form-control @error('stock_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Stock') }}</option>
                                        @foreach($stocks as $stock)
                                            <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                                                {{ optional($stock->product)->productName ?? 'N/A' }} - Stock: {{ $stock->productStock }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stock_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if(auth()->user()->accessToMultiBranch())
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="branch_id">{{ __('Branch') }}</label>
                                    <select name="branch_id" id="branch_id" class="form-control">
                                        <option value="">{{ __('Select Branch') }}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="purchase_price">{{ __('Purchase Price') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="purchase_price" id="purchase_price" 
                                           class="form-control @error('purchase_price') is-invalid @enderror" 
                                           step="0.01" min="0" required
                                           value="{{ old('purchase_price') }}"
                                           placeholder="{{ __('Enter purchase price') }}">
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="quantity">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" 
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           step="0.01" min="0" required
                                           value="{{ old('quantity') }}"
                                           placeholder="{{ __('Enter quantity') }}">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Save Combo Product') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
