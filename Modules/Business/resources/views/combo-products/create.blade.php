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
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('business.combo-products.index') }}" class="save-publish-btn">
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>

                    <div class="order-form-section p-16">
                        <form action="{{ route('business.combo-products.store') }}" method="POST" class="ajaxform_instant_reload">
                            @csrf
                            <div class="add-suplier-modal-wrapper d-block">
                                <div class="row">
                                    <div class="col-lg-6 mb-2">
                                        <label for="product_id">{{ __('Select Product') }} <span class="text-danger">*</span></label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="product_id" id="product_id" class="form-control table-select w-100 @error('product_id') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Product') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->productName }} ({{ $product->productCode }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span></span>
                                        </div>
                                        @error('product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 mb-2">
                                        <label for="stock_id">{{ __('Select Stock') }} <span class="text-danger">*</span></label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="stock_id" id="stock_id" class="form-control table-select w-100 @error('stock_id') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Stock') }}</option>
                                                @foreach($stocks as $stock)
                                                    <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                                                        {{ optional($stock->product)->productName ?? __('N/A') }} - {{ __('Stock') }}: {{ $stock->productStock }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span></span>
                                        </div>
                                        @error('stock_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if(auth()->user()->accessToMultiBranch())
                                    <div class="col-lg-6 mb-2">
                                        <label for="branch_id">{{ __('Branch') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="branch_id" id="branch_id" class="form-control table-select w-100">
                                                <option value="">{{ __('Select Branch') }}</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-lg-6 mb-2">
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

                                    <div class="col-lg-6 mb-2">
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

                                    <div class="col-lg-12 mb-2">
                                        <button type="submit" class="save-publish-btn submit-btn">
                                            {{ __('Save Combo Product') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
