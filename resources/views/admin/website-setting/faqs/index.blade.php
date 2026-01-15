@extends('layouts.master')

@section('title')
    {{__('FAQS List') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('FAQs List') }}</h4>
                        <a href="{{ route('admin.faqs.create') }}" class="theme-btn print-btn text-light">
                            <i class="far fa-plus" aria-hidden="true"></i>
                            {{ __('Create New') }}
                        </a>
                    </div>
                    <div class="table-top-form p-16-0">
                        <form action="{{ route('admin.faqs.filter') }}" method="post" class="filter-form" table="#faqs-data">
                            @csrf
                            <div class="table-top-left d-flex gap-3 margin-l-16">
                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="per_page" class="form-control">
                                        <option value="5" selected>{{ __('Show- 5') }}</option>
                                        <option value="10">{{ __('Show- 10') }}</option>
                                        <option value="25">{{ __('Show- 25') }}</option>
                                        <option value="50">{{ __('Show- 50') }}</option>
                                        <option value="100">{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>
                                <div class="table-search position-relative">
                                    <input class="form-control" type="text" name="search" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
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

                <div id="faqs-data">
                    @include('admin.website-setting.faqs.datas')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('modal')
    @include('admin.components.multi-delete-modal')

    <div class="modal fade" id="view-single-details" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('View Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 mt-3">
                        <label for="view-question" class="form-label fw-bold">{{ __('Question') }}:</label>
                        <h6 id="view-question"></h6>
                    </div>
                    <div class="mb-3">
                        <label for="view-answer" class="form-label fw-bold">{{ __('Answer') }}:</label>
                        <p id="view-answer"></p>
                    </div>
                    <div class="mb-3">
                        <label for="view-status" class="form-label fw-bold">{{ __('Status') }}:</label>
                        <p id="view-status"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush
