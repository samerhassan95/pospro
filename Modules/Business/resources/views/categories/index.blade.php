@extends('layouts.business.master')

@section('title')
    {{ __('Category List') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('Category List') }}</h4>
                        @usercan('categories.create')
                        <a type="button" href="#category-create-modal"
                            class="add-order-btn rounded-2 {{ Route::is('admin.categories.create') ? 'active' : '' }}"
                            class="btn btn-primary" data-bs-toggle="modal"><i
                            class="fas fa-plus-circle me-1"></i>{{ __('Add new Category') }}</a>
                        @endusercan
                    </div>
                    <div class="table-top-form p-16-0">
                        <form action="{{ route('business.categories.filter') }}" method="post" class="filter-form"
                            table="#business-category-data">
                            @csrf
                            <div class="table-top-left d-flex gap-3 margin-l-16">
                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="per_page" class="form-control">
                                        <option value="5" selected>{{ __('Show- 5') }}</option>
                                        <option value="10" >{{ __('Show- 10') }}</option>
                                        <option value="25">{{ __('Show- 25') }}</option>
                                        <option value="50">{{ __('Show- 50') }}</option>
                                        <option value="100">{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>
                                <div class="table-search position-relative">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="{{ __('Search...') }}">
                                    <span class="position-absolute">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                            </svg>

                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="delete-item delete-show d-none">
                    <div class="delete-item-show">
                        <p class="fw-bold"><span class="selected-count"></span> {{ __('items show') }}</p>
                        <button data-bs-toggle="modal" class="trigger-modal" data-bs-target="#multi-delete-modal" data-url="{{ route('business.categories.delete-all') }}">{{ __('Delete') }}</button>
                    </div>
                </div>

                <div id="business-category-data">
                    @include('business::categories.datas')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include('business::component.delete-modal')
    @include('business::categories.create')
    @include('business::categories.edit')
@endpush

@push('js')
{{-- Pass variations data to JavaScript --}}
<input type="hidden" id="all-variations-data" value='@json($variations ?? [])'>

<script>
$(document).ready(function() {
    const variationsData = JSON.parse($('#all-variations-data').val() || '[]');
    
    console.log('All variations:', variationsData);
    console.log('Variations count:', variationsData.length);
    
    // Function to render variation checkboxes
    function renderVariationCheckboxes(containerId, categoryData = null) {
        const $container = $(containerId);
        
        if ($container.length === 0) {
            console.error('Container not found:', containerId);
            return;
        }
        
        $container.empty();
        
        console.log('Rendering variations in:', containerId);
        console.log('Category data:', categoryData);
        console.log('Variations from database:', variationsData);
        
        // Only show variations from the variations table
        variationsData.forEach(variation => {
            if (!variation.name) return;
            
            const isChecked = categoryData && categoryData.custom_variations && 
                            Array.isArray(categoryData.custom_variations) && 
                            categoryData.custom_variations.includes(variation.name) ? 'checked' : '';
            
            const valuesCount = variation.values ? variation.values.length : 0;
            const valueInfo = ` (${valuesCount} values)`;
            
            const uniqueId = `${variation.name}Check_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            
            const checkbox = `
                <div class="select-variations-content">
                    <input class="form-check-input variations-input" type="checkbox" 
                           name="variation${variation.name}" value="true" 
                           id="${uniqueId}" ${isChecked}>
                    <label class="form-check-label variations-label" for="${uniqueId}">
                        ${variation.name}${valueInfo}
                    </label>
                </div>
            `;
            $container.append(checkbox);
        });
        
        console.log('Rendered', variationsData.length, 'variations');
    }
    
    // Render when modals are shown
    $('#category-create-modal').on('shown.bs.modal', function() {
        console.log('Create modal shown - event triggered');
        setTimeout(function() {
            console.log('Attempting to render in create modal');
            renderVariationCheckboxes('#variations-checkboxes-container-create');
        }, 100);
    });
    
    $('#category-edit-modal').on('shown.bs.modal', function() {
        console.log('Edit modal shown - event triggered');
        setTimeout(function() {
            console.log('Attempting to render in edit modal');
            renderVariationCheckboxes('#variations-checkboxes-container');
        }, 100);
    });
    
    // Also try with click events as backup
    $(document).on('click', '[data-bs-target="#category-create-modal"]', function() {
        console.log('Create button clicked');
        setTimeout(function() {
            renderVariationCheckboxes('#variations-checkboxes-container-create');
        }, 500);
    });
    
    $(document).on('click', '[data-bs-target="#category-edit-modal"]', function() {
        console.log('Edit button clicked');
        setTimeout(function() {
            renderVariationCheckboxes('#variations-checkboxes-container');
        }, 500);
    });
});
</script>
@endpush
