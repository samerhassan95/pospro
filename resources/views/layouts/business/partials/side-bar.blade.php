<nav class="side-bar">
    <div class="side-bar-logo">
        <a href="{{ route('business.dashboard.index') }}" class="logo-link">
            <img src="{{ asset(get_admin_logo()) }}" alt="Logo" class="sidebar-logo-img"> 
            <span class="sidebar-logo-text"><span class="bytes-text">{{ get_system_title() }}</span></span>
        </a>
        <button class="close-btn"><i class="fal fa-times"></i></button>
    </div>
    <div class="side-bar-manu">
        <ul>
            @usercan('dashboard.read')
            <li class="{{ Request::routeIs('business.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('business.dashboard.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.63606 9.45776L3.33268 9.63192L3.7134 13.4168C3.92852 15.5553 4.03607 16.6246 4.75018 17.2704C5.46429 17.9163 6.53896 17.9163 8.68827 17.9163H11.3104C13.4598 17.9163 14.5344 17.9163 15.2485 17.2704C15.9626 16.6246 16.0702 15.5553 16.2853 13.4168L16.666 9.63192L17.3627 9.45776C17.9328 9.31526 18.3327 8.80301 18.3327 8.21536C18.3327 7.79746 18.1288 7.40586 17.7864 7.16621L10.9551 2.38429C10.3813 1.98258 9.61743 1.98258 9.0436 2.38429L2.21226 7.16621C1.86991 7.40586 1.66602 7.79746 1.66602 8.21536C1.66602 8.80301 2.06596 9.31526 2.63606 9.45776Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.99935 14.1667C11.1499 14.1667 12.0827 13.2339 12.0827 12.0833C12.0827 10.9327 11.1499 10 9.99935 10C8.84876 10 7.91602 10.9327 7.91602 12.0833C7.91602 13.2339 8.84876 14.1667 9.99935 14.1667Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Dashboard') }}
                </a>
            </li>
            @endusercan

            @usercanany(['sales.read', 'sales.create'])
            <li class="dropdown {{ Request::routeIs('business.sales.index', 'business.sales.create', 'business.sales.edit', 'business.sale-returns.create', 'business.sale-returns.index','business.sales.inventory') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.66667 13.3337H12.7193C16.459 13.3337 17.0277 10.9843 17.7175 7.55789C17.9165 6.56958 18.016 6.07543 17.7767 5.74622C17.5375 5.41699 17.0789 5.41699 16.1617 5.41699H5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M6.66683 13.3337L4.48244 2.92943C4.29695 2.18748 3.63031 1.66699 2.86554 1.66699H2.0835" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M7.4 13.333H7.05714C5.92102 13.333 5 14.2924 5 15.4758C5 15.6731 5.1535 15.833 5.34286 15.833H14.5833" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.75 18.333C9.44036 18.333 10 17.7734 10 17.083C10 16.3927 9.44036 15.833 8.75 15.833C8.05964 15.833 7.5 16.3927 7.5 17.083C7.5 17.7734 8.05964 18.333 8.75 18.333Z" stroke="currentColor" stroke-width="1.25"/>
                            <path d="M14.5835 18.333C15.2739 18.333 15.8335 17.7734 15.8335 17.083C15.8335 16.3927 15.2739 15.833 14.5835 15.833C13.8931 15.833 13.3335 16.3927 13.3335 17.083C13.3335 17.7734 13.8931 18.333 14.5835 18.333Z" stroke="currentColor" stroke-width="1.25"/>
                        </svg>
                    </span>
                    {{ __('Sales') }}</a>
                <ul>
                    @usercan('sales.create')
                    <li>
                        <a class="{{ Request::routeIs('business.sales.create') ? 'active' : '' }}" href="{{ route('business.sales.create') }}">
                            {{ __('POS') }}
                        </a>
                    </li>
                    @endusercan

                    @usercan('inventory.create')
                    <li>
                        <a class="{{ Request::routeIs('business.sales.inventory') ? 'active' : '' }}" href="{{ route('business.sales.inventory') }}">
                            {{ __('Inventory') }}
                        </a>
                    </li>
                    @endusercan

                    @usercan('sales.read')
                    <li><a class="{{ Request::routeIs('business.sales.index', 'business.sale-returns.create') ? 'active' : '' }}" href="{{ route('business.sales.index') }}">{{ __('Sales List') }}</a></li>
                    @endusercan

                    @usercan('sale-returns.read')
                    <li><a class="{{ Request::routeIs('business.sale-returns.index') ? 'active' : '' }}" href="{{ route('business.sale-returns.index') }}">{{ __('Sales Return') }}</a></li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            @usercanany(['purchases.read', 'purchase-returns.read'])
                <li class="dropdown {{ Request::routeIs('business.purchases.index', 'business.purchases.create', 'business.purchases.edit', 'business.purchase-returns.create', 'business.purchase-returns.index') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.33398 15.5385V6.71221C3.33398 4.33387 3.33398 3.1447 4.06622 2.40585C4.79845 1.66699 5.97696 1.66699 8.33398 1.66699H11.6673C14.0243 1.66699 15.2028 1.66699 15.9351 2.40585C16.6673 3.1447 16.6673 4.33387 16.6673 6.71221V15.5385C16.6673 16.7982 16.6673 17.4281 16.2823 17.676C15.6532 18.0812 14.6807 17.2315 14.1916 16.9231C13.7874 16.6682 13.5854 16.5407 13.3611 16.5334C13.1187 16.5254 12.9131 16.6477 12.4764 16.9231L10.884 17.9273C10.4544 18.1982 10.2397 18.3337 10.0007 18.3337C9.76165 18.3337 9.5469 18.1982 9.11732 17.9273L7.52493 16.9231C7.12078 16.6682 6.9187 16.5407 6.69443 16.5334C6.45208 16.5254 6.24643 16.6477 5.80971 16.9231C5.32061 17.2315 4.34804 18.0812 3.71894 17.676C3.33398 17.4281 3.33398 16.7982 3.33398 15.5385Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.3327 5H6.66602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.33268 8.33398H6.66602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12.084 8.22917C11.3937 8.22917 10.834 8.71883 10.834 9.32292C10.834 9.927 11.3937 10.4167 12.084 10.4167C12.7743 10.4167 13.334 10.9063 13.334 11.5104C13.334 12.1145 12.7743 12.6042 12.084 12.6042M12.084 8.22917C12.6282 8.22917 13.0912 8.5335 13.2628 8.95833M12.084 8.22917V7.5M12.084 12.6042C11.5397 12.6042 11.0767 12.2998 10.9052 11.875M12.084 12.6042V13.3333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            </svg>
                        </span>
                        {{ __('Purchases') }}</a>
                    <ul>
                        @usercan('purchases.create')
                        <li>
                            <a class="{{ Request::routeIs('business.purchases.create') ? 'active' : '' }}" href="{{ route('business.purchases.create') }}">{{ __('Add Purchase')}}</a>
                        </li>
                        @endusercan

                        @usercan('purchases.read')
                        <li><a class="{{ Request::routeIs('business.purchases.index',  'business.purchase-returns.create') ? 'active' : '' }}"
                                href="{{ route('business.purchases.index') }}">{{ __('Purchase List') }}</a></li>
                        @endusercan

                        @usercan('purchase-returns.read')
                        <li><a class="{{ Request::routeIs('business.purchase-returns.index') ? 'active' : '' }}"
                                href="{{ route('business.purchase-returns.index') }}">{{ __('Returns List') }}</a></li>
                        @endusercan

                    </ul>
                </li>
            @endusercanany

            @usercanany(['products.read', 'bulk-uploads.read', 'categories.read', 'brands.read', 'units.read', 'product-models.read'])
                <li class="dropdown {{ Request::routeIs('business.products.index', 'business.products.create', 'business.products.edit', 'business.products.expired', 'business.categories.index', 'business.brands.index', 'business.units.index', 'business.barcodes.index', 'business.bulk-uploads.index', 'business.variations.index', 'business.product-models.index','business.racks.index', 'business.shelfs.index'/*, 'business.combo-products.index'*/) ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.8327 18.3327C10.1508 18.3327 9.49952 18.0483 8.19677 17.4797C6.67629 16.816 5.5123 16.3079 4.70475 15.8327H1.66602M10.8327 18.3327C11.5145 18.3327 12.1658 18.0483 13.4686 17.4797C16.7113 16.0643 18.3327 15.3565 18.3327 14.166V5.41602M10.8327 18.3327V9.16602M3.33268 5.41602V7.91602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.77225 8.0755L5.33792 6.89756C4.00196 6.2511 3.33398 5.92787 3.33398 5.41602C3.33398 4.90416 4.00196 4.58093 5.33792 3.93447L7.77225 2.75653C9.27465 2.02952 10.0259 1.66602 10.834 1.66602C11.6421 1.66602 12.3933 2.02952 13.8957 2.75653L16.3301 3.93447C17.666 4.58093 18.334 4.90416 18.334 5.41602C18.334 5.92787 17.666 6.2511 16.3301 6.89756L13.8957 8.0755C12.3933 8.80252 11.6421 9.16602 10.834 9.16602C10.0259 9.16602 9.27465 8.80252 7.77225 8.0755Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.1145 3.3457L6.55664 7.48672" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M1.66602 10.834H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M1.66602 13.334H4.16602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Products') }}</a>
                    <ul>
                        @usercan('products.read')
                        <li><a class="{{ Request::routeIs('business.products.index') ? 'active' : '' }}"
                                href="{{ route('business.products.index') }}">{{ __('All Product') }}</a>
                        </li>
                        @endusercan

                        @usercan('products.create')
                        <li>
                            <a class="{{ Request::routeIs('business.products.create') ? 'active' : '' }}" href="{{ route('business.products.create') }}">{{ __('Add Product') }}</a>
                        </li>
                        @endusercan

                        {{-- @usercan('products.read')
                        <li><a class="{{ Request::routeIs('business.combo-products.index') ? 'active' : '' }}"
                                href="{{ route('business.combo-products.index') }}">{{ __('Combo Products') }}</a>
                        </li>
                        @endusercan --}}

                        @usercan('products-expired.read')
                         <li><a class="{{ Request::routeIs('business.products.expired') ? 'active' : '' }}" href="{{ route('business.products.expired') }}">{{ __('Expired Products') }}</a></li>
                        @endusercan

                        @usercan('barcodes.read')
                        <li>
                            <a class="{{ Request::routeIs('business.barcodes.index') ? 'active' : '' }}"
                               href="{{ route('business.barcodes.index') }}">{{ __('Print Labels') }}</a>
                        </li>
                        @endusercan

                        @usercan('bulk-uploads.read')
                        <li>
                            <a class="{{ Request::routeIs('business.bulk-uploads.index') ? 'active' : '' }}"
                               href="{{ route('business.bulk-uploads.index') }}">{{ __('Bulk Upload') }}</a>
                        </li>
                        @endusercan

                        @usercan('categories.read')
                        <li>
                            <a class="{{ Request::routeIs('business.categories.index') ? 'active' : '' }}"
                                href="{{ route('business.categories.index') }}">{{ __('Category') }}</a>
                        </li>
                        @endusercan

                        @usercan('brands.read')
                        <li>
                            <a class="{{ Request::routeIs('business.brands.index') ? 'active' : '' }}"
                                href="{{ route('business.brands.index') }}">{{ __('Brand') }}</a>
                        </li>
                        @endusercan

                        @usercan('product-models.read')
                        <li>
                            <a class="{{ Request::routeIs('business.product-models.index') ? 'active' : '' }}"
                                href="{{ route('business.product-models.index') }}">{{ __('Model') }}</a>
                        </li>
                        @endusercan

                        @usercan('variations.read')
                        <li>
                            <a class="{{ Request::routeIs('business.variations.index') ? 'active' : '' }}" href="{{ route('business.variations.index') }}">{{ __('Variation') }}</a>
                        </li>
                        @endusercan

                        @usercan('units.read')
                        <li>
                            <a class="{{ Request::routeIs('business.units.index') ? 'active' : '' }}"
                                href="{{ route('business.units.index') }}">{{ __('Unit') }}</a>
                        </li>
                        @endusercan

                       @usercan('racks.read')
                        <li>
                            <a class="{{ Request::routeIs('business.racks.index') ? 'active' : '' }}" href="{{ route('business.racks.index') }}">{{ __('Racks') }}</a>
                        </li>
                        @endusercan

                        @usercan('shelfs.read')
                        <li>
                            <a class="{{ Request::routeIs('business.shelfs.index') ? 'active' : '' }}" href="{{ route('business.shelfs.index') }}">{{ __('Shelfs') }}</a>
                        </li>
                        @endusercan

                    </ul>
                </li>
            @endusercanany

            @if (moduleCheck('WarehouseAddon'))
             @usercan('warehouses.read')
                <li class="dropdown {{ Request::routeIs('warehouse.warehouses.index','warehouse.warehouses.product') ? 'active' : '' }}">
                    <a class="position-relative" href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.08398 13.7494C7.66618 12.743 8.75437 12.0658 10.0006 12.0658C11.247 12.0658 12.3351 12.743 12.9173 13.7494M11.6674 8.33268C11.6674 9.25318 10.9211 9.99937 10.0007 9.99937C9.0802 9.99937 8.33401 9.25318 8.33401 8.33268C8.33401 7.41221 9.0802 6.66602 10.0007 6.66602C10.9211 6.66602 11.6674 7.41221 11.6674 8.33268Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                                <path d="M18.3327 11.6397V8.36165C15.9521 8.36165 14.4041 5.77599 15.6093 3.72307L12.7226 2.08407C11.5025 4.16236 8.49752 4.16227 7.27737 2.08398L4.39062 3.72299C5.5959 5.77594 4.04662 8.36165 1.66602 8.36165V11.6397C4.04658 11.6397 5.59456 14.2254 4.38931 16.2783L7.27606 17.9173C8.49677 15.838 11.5032 15.8379 12.7239 17.9172L15.6107 16.2782C14.4055 14.2253 15.9522 11.6397 18.3327 11.6397Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Warehouse') }}
                        @if (env('DEMO_MODE'))
                         <sup class="badge bg-warning position-absolute side-bar-addon">{{__('Add-On')}}</sup>
                        @endif
                    </a>

                    @usercan('warehouses.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('warehouse.warehouses.index') ? 'active' : '' }}" href="{{ route('warehouse.warehouses.index') }}">{{ __('Warehouse') }}</a>
                        </li>
                    </ul>
                    @endusercan

                    @usercan('warehouses.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('warehouse.warehouses.product') ? 'active' : '' }}" href="{{ route('warehouse.warehouses.product') }}">{{ __('Products') }}</a>
                        </li>
                    </ul>
                    @endusercan

                </li>
             @endusercan
            @endif

           @if ((moduleCheck('MultiBranchAddon') && ((plan_data()['allow_multibranch'] ?? false)) || moduleCheck('WarehouseAddon')))
           @usercan('transfers.read')
            <li class="{{ Request::routeIs('business.transfers.index','business.transfers.create','business.transfers.edit') ? 'active' : '' }}">
                <a href="{{ route('business.transfers.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.08398 10.0026C2.08398 6.27065 2.08398 4.40468 3.24335 3.2453C4.40273 2.08594 6.2687 2.08594 10.0007 2.08594C13.7326 2.08594 15.5986 2.08594 16.758 3.2453C17.9173 4.40468 17.9173 6.27065 17.9173 10.0026C17.9173 13.7345 17.9173 15.6005 16.758 16.7599C15.5986 17.9193 13.7326 17.9193 10.0007 17.9193C6.2687 17.9193 4.40273 17.9193 3.24335 16.7599C2.08398 15.6005 2.08398 13.7345 2.08398 10.0026Z" stroke="currentColor" stroke-width="1.25"/>
                            <path d="M8.26299 6.66797L6.71429 7.7656C6.37259 8.00778 6.20175 8.12886 6.26187 8.23175C6.32201 8.33464 6.56363 8.33464 7.04686 8.33464H13.75" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.737 13.3346L13.2857 12.237C13.6274 11.9948 13.7983 11.8737 13.7381 11.7709C13.678 11.668 13.4364 11.668 12.9532 11.668H6.25" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Transfer') }}
                </a>
            </li>
            @endusercan
            @endif

            @if (moduleCheck('MultiBranchAddon') && (plan_data()['allow_multibranch'] ?? false))
            @usercan('branches.read')
            <li class="dropdown {{ Request::routeIs('multibranch.branches.index', 'multibranch.branches.overview', 'business.roles.index', 'business.roles.edit', 'business.roles.create') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.47266 8.74609V12.9143C2.47266 15.2721 2.47266 16.451 3.20489 17.1835C3.93712 17.9161 5.11563 17.9161 7.47266 17.9161H12.4727C14.8297 17.9161 16.0082 17.9161 16.7404 17.1835C17.4727 16.451 17.4727 15.2721 17.4727 12.9143V8.74609" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M12.4727 14.1602C11.9026 14.6662 10.995 14.9935 9.97266 14.9935C8.95032 14.9935 8.04273 14.6662 7.47266 14.1602" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M8.42016 7.01369C8.18517 7.86236 7.33056 9.32629 5.7068 9.53846C4.2731 9.72579 3.18571 9.09996 2.90796 8.83829C2.60172 8.62612 1.90379 7.94722 1.73287 7.52289C1.56195 7.09856 1.76136 6.17917 1.90379 5.80434L2.47318 4.15569C2.61218 3.74159 2.93757 2.76217 3.27116 2.4309C3.60476 2.09962 4.28016 2.08521 4.55816 2.08521H10.3961C11.8987 2.10644 15.1844 2.07181 15.8339 2.08521C16.4834 2.09861 16.8737 2.64306 16.9877 2.87645C17.9567 5.2238 18.3337 6.56814 18.3337 7.14099C18.2072 7.75209 17.6837 8.90437 15.8339 9.41121C13.9114 9.93787 12.8215 8.91337 12.4796 8.52004M7.62967 8.52004C7.9003 8.85246 8.74924 9.52154 9.97982 9.53846C11.2105 9.55546 12.2731 8.69687 12.6505 8.26547C12.7573 8.13817 12.9881 7.76051 13.2274 7.01369" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Branch') }}
                    @if (env('DEMO_MODE'))
                    <sup class="badge bg-warning position-absolute side-bar-addon-2">{{__('Add-On')}}</sup>
                    @endif
                </a>
                <ul>
                    <li>
                        <a class="{{ Request::routeIs('multibranch.branches.overview') ? 'active' : '' }}" href="{{ route('multibranch.branches.overview') }}">{{ __('Overview') }}</a>
                    </li>
                    <li>
                        <a class="{{ Request::routeIs('multibranch.branches.index') ? 'active' : '' }}" href="{{ route('multibranch.branches.index') }}">{{ __('Branch List') }}</a>
                    </li>
                    <li>
                        <a class="{{ Request::routeIs('business.roles.index', 'business.roles.edit', 'business.roles.create') ? 'active' : '' }}" href="{{ route('business.roles.index') }}">{{ __('Role & permissions') }}</a>
                    </li>
                </ul>
            </li>
            @endusercan
            @endif

            @usercanany(['stocks.read', 'expired-products.read'])
            <li class="dropdown {{ Request::routeIs('business.stocks.index','business.expired-products.index') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 18.333C9.31817 18.333 8.66683 18.0578 7.36411 17.5076C4.12137 16.1378 2.5 15.4528 2.5 14.3008C2.5 13.9782 2.5 8.38676 2.5 5.83301M10 18.333C10.6818 18.333 11.3332 18.0578 12.6359 17.5076C15.8787 16.1378 17.5 15.4528 17.5 14.3008V5.83301M10 18.333V9.46201" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.93827 8.07647L4.50393 6.89853C3.16797 6.25208 2.5 5.92885 2.5 5.41699C2.5 4.90513 3.16797 4.58191 4.50393 3.93545L6.93827 2.75751C8.44067 2.0305 9.19192 1.66699 10 1.66699C10.8081 1.66699 11.5593 2.03049 13.0617 2.75751L15.4961 3.93545C16.832 4.58191 17.5 4.90513 17.5 5.41699C17.5 5.92885 16.832 6.25208 15.4961 6.89853L13.0617 8.07647C11.5593 8.80349 10.8081 9.16699 10 9.16699C9.19192 9.16699 8.44067 8.80349 6.93827 8.07647Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 10L6.66667 10.8333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.1673 3.33398L5.83398 7.50065" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Stock List') }}
                </a>
                <ul>
                    @usercan('stocks.read')
                    <li>
                        <a class="{{ Request::routeIs('business.stocks.index') && !request('alert_qty')  ? 'active' : '' }}" href="{{ route('business.stocks.index') }}">{{ __('All Stock') }}</a>
                    </li>
                    @endusercan
                    @usercan('stocks.read')
                    <li>
                        <a class="{{ Request::routeIs('business.stocks.index') && request('alert_qty') ? 'active' : '' }}" href="{{ route('business.stocks.index', ['alert_qty' => true]) }}">{{ __('Low Stock') }}</a>
                    </li>
                    @endusercan
                    @usercan('expired-products.read')
                    <li>
                        <a class="{{ Request::routeIs('business.expired-products.index') ? 'active' : '' }}" href="{{ route('business.expired-products.index') }}">{{ __('Expired Products') }}</a>
                    </li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            @usercanany(['parties.read', 'parties.create'])
            <li class="dropdown {{ (Request::routeIs('business.parties.index') && request('type') == 'Customer') || (Request::routeIs('business.parties.create') && request('type') == 'Customer') || (Request::routeIs('business.parties.edit') && request('type') == 'Customer') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.3117 15C17.9361 15 18.4327 14.6071 18.8787 14.0576C19.7916 12.9328 18.2927 12.034 17.7211 11.5938C17.14 11.1463 16.4912 10.8928 15.8333 10.8333M15 9.16667C16.1506 9.16667 17.0833 8.23392 17.0833 7.08333C17.0833 5.93274 16.1506 5 15 5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M2.68895 15C2.06453 15 1.56788 14.6071 1.12194 14.0576C0.20906 12.9328 1.70788 12.034 2.27952 11.5938C2.86063 11.1463 3.50947 10.8928 4.16732 10.8333M4.58399 9.16667C3.4334 9.16667 2.50065 8.23392 2.50065 7.08333C2.50065 5.93274 3.4334 5 4.58399 5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                            <path d="M6.73715 12.593C5.88567 13.1195 3.65314 14.1946 5.0129 15.5398C5.67714 16.197 6.41692 16.667 7.347 16.667H12.6543C13.5844 16.667 14.3242 16.197 14.9884 15.5398C16.3482 14.1946 14.1157 13.1195 13.2642 12.593C11.2674 11.3583 8.73384 11.3583 6.73715 12.593Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.9173 6.24967C12.9173 7.86051 11.6115 9.16634 10.0007 9.16634C8.38983 9.16634 7.08398 7.86051 7.08398 6.24967C7.08398 4.63884 8.38983 3.33301 10.0007 3.33301C11.6115 3.33301 12.9173 4.63884 12.9173 6.24967Z" stroke="currentColor" stroke-width="1.25"/>
                        </svg>
                    </span>
                    {{ __('Customers') }}
                </a>
                <ul>
                    @usercan('parties.read')
                    <li><a class="{{ Request::routeIs('business.parties.index') && request('type') == 'Customer' ? 'active' : '' }}" href="{{ route('business.parties.index', ['type' => 'Customer']) }}">{{ __('All Customers') }}</a>
                    </li>
                    @endusercan
                    @usercan('parties.create')
                    <li><a class="{{ Request::routeIs('business.parties.create') && request('type') == 'Customer' ? 'active' : '' }}" href="{{ route('business.parties.create', ['type' => 'Customer']) }}">{{ __('Add Customer') }}</a>
                    </li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            @usercanany(['parties.read', 'parties.create'])
            <li class="dropdown {{ (Request::routeIs('business.parties.index') && request('type') == 'Supplier') || (Request::routeIs('business.parties.create') && request('type') == 'Supplier') || (Request::routeIs('business.parties.edit') && request('type') == 'Supplier') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.8327 5.83333C10.8327 7.67428 9.34027 9.16667 7.49935 9.16667C5.6584 9.16667 4.16602 7.67428 4.16602 5.83333C4.16602 3.99238 5.6584 2.5 7.49935 2.5C9.34027 2.5 10.8327 3.99238 10.8327 5.83333Z" stroke="currentColor" stroke-width="1.25"/>
                            <path d="M12.5 9.16667C14.3409 9.16667 15.8333 7.67428 15.8333 5.83333C15.8333 3.99238 14.3409 2.5 12.5 2.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.16602 11.666H5.83268C3.5315 11.666 1.66602 13.5315 1.66602 15.8327C1.66602 16.7532 2.41221 17.4993 3.33268 17.4993H11.666C12.5865 17.4993 13.3327 16.7532 13.3327 15.8327C13.3327 13.5315 11.4672 11.666 9.16602 11.666Z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
                            <path d="M14.166 11.666C16.4672 11.666 18.3327 13.5315 18.3327 15.8327C18.3327 16.7532 17.5865 17.4993 16.666 17.4993H15.416" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Suppliers') }}
                </a>
                <ul>
                    @usercan('parties.read')
                    <li>
                        <a class="{{ Request::routeIs('business.parties.index') && request('type') == 'Supplier' ? 'active' : '' }}" href="{{ route('business.parties.index', ['type' => 'Supplier']) }}">{{ __('All Suppliers') }}</a>
                    </li>
                    @endusercan
                    @usercan('parties.create')
                    <li>
                        <a class="{{ Request::routeIs('business.parties.create') && request('type') == 'Supplier' ? 'active' : '' }}" href="{{ route('business.parties.create', ['type' => 'Supplier']) }}">{{ __('Add Supplier') }}</a>
                    </li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            @usercan('vats.read')
            <li class="{{ Request::routeIs('business.vats.index') ? 'active' : '' }}">
                <a href="{{ route('business.vats.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5159 7.86104L12.9558 8.74825C13.0158 8.87175 13.1758 8.99017 13.3108 9.01284L14.1082 9.14642C14.6181 9.23217 14.7381 9.60517 14.3706 9.97309L13.7507 10.5982C13.6457 10.704 13.5882 10.9082 13.6207 11.0543L13.7982 11.8281C13.9382 12.4406 13.6157 12.6775 13.0783 12.3574L12.3309 11.9113C12.1959 11.8307 11.9735 11.8307 11.836 11.9113L11.0886 12.3574C10.5536 12.6775 10.2286 12.4381 10.3686 11.8281L10.5461 11.0543C10.5786 10.9082 10.5211 10.704 10.4161 10.5982L9.79622 9.97309C9.43122 9.60517 9.54872 9.23217 10.0586 9.14642L10.8561 9.01284C10.9886 8.99017 11.1486 8.87175 11.2086 8.74825L11.6485 7.86104C11.8885 7.37965 12.2784 7.37965 12.5159 7.86104Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.66602 14.166V17.0827" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.66602 2.91602V5.83268" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3343 7.39847C18.2785 6.11341 18.1223 5.27683 17.6845 4.61505C17.4327 4.23432 17.12 3.90317 16.7603 3.63658C15.7882 2.91602 14.4168 2.91602 11.6741 2.91602H8.3285C5.58577 2.91602 4.21441 2.91602 3.24231 3.63658C2.88266 3.90317 2.56985 4.23432 2.31801 4.61505C1.88034 5.27676 1.72409 6.11322 1.66831 7.39804C1.65877 7.61775 1.84802 7.78581 2.05538 7.78581C3.2102 7.78581 4.14636 8.77685 4.14636 9.99935C4.14636 11.2218 3.2102 12.2128 2.05538 12.2128C1.84802 12.2128 1.65877 12.3809 1.66831 12.6007C1.72409 13.8855 1.88034 14.7219 2.31801 15.3837C2.56985 15.7643 2.88266 16.0955 3.24231 16.3621C4.21441 17.0827 5.58577 17.0827 8.32851 17.0827H11.6741C14.4168 17.0827 15.7882 17.0827 16.7603 16.3621C17.12 16.0955 17.4327 15.7643 17.6845 15.3837C18.1223 14.7218 18.2785 13.8853 18.3343 12.6003V7.39847Z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Tax Setting') }}
                </a>
            </li>
            @endusercan

            @usercan('dues.read')
                <li class="dropdown {{ Request::routeIs('business.dues.index'/*,'business.walk-dues.index','business.collect.walk.dues'*/,'business.collect.dues', 'business.party.dues') ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.23326 12.7463C3.21802 13.3606 0.556162 14.6148 2.17742 16.1843C2.96938 16.951 3.85144 17.4993 4.96039 17.4993H11.2883C12.3973 17.4993 13.2793 16.951 14.0713 16.1843C15.6925 14.6148 13.0307 13.3606 12.0154 12.7463C9.63477 11.3059 6.61395 11.3059 4.23326 12.7463Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.2507 5.83333C11.2507 7.67428 9.75823 9.16667 7.91732 9.16667C6.07637 9.16667 4.58398 7.67428 4.58398 5.83333C4.58398 3.99238 6.07637 2.5 7.91732 2.5C9.75823 2.5 11.2507 3.99238 11.2507 5.83333Z" stroke="currentColor" stroke-width="1.25"/>
                                <path d="M14.166 4.16602H18.3327" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.166 6.66602H18.3327" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.666 9.16602H18.3327" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Due List') }}
                    </a>
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('business.dues.index') ? 'active' : '' }}" href="{{ route('business.dues.index') }}">{{ __('All Due') }}</a>
                        </li>

                        {{-- <li>
                            <a class="{{ Request::routeIs('business.walk-dues.index','business.collect.walk.dues') ? 'active' : '' }}" href="{{ route('business.walk-dues.index') }}">{{ __('Guest Due') }}</a>
                        </li> --}}

                        <li class="{{ (Request::routeIs('business.party.dues') && request('type') == 'Retailer') ? 'active' : '' }}">
                            <a href="{{ route('business.party.dues', ['type' => 'Retailer']) }}">
                                {{ __('Customer Due') }}
                            </a>
                        </li>

                        <li class="{{ (Request::routeIs('business.party.dues') && request('type') == 'Dealer') ? 'active' : '' }}">
                            <a href="{{ route('business.party.dues', ['type' => 'Dealer']) }}">
                                {{ __('Dealer Due') }}
                            </a>
                        </li>

                        <li class="{{ (Request::routeIs('business.party.dues') && request('type') == 'Wholesaler') ? 'active' : '' }}">
                            <a href="{{ route('business.party.dues', ['type' => 'Wholesaler']) }}">
                                {{ __('Wholesaler Due') }}
                            </a>
                        </li>

                        <li class="{{ (Request::routeIs('business.party.dues') && request('type') == 'Supplier') ? 'active' : '' }}">
                            <a href="{{ route('business.party.dues', ['type' => 'Supplier']) }}">
                                {{ __('Supplier Due') }}
                            </a>
                        </li>

                    </ul>
                </li>
            @endusercan

            <li class="dropdown {{ Request::routeIs(/*'business.banks.index', 'business.banks.create','business.cashes.index', 'business.cheques.index', 'business.bank-transactions.index', 'business.loss-profit-history.index', 'business.transactions.index',*/ 'business.incomes.index', 'business.income-categories.index', 'business.expenses.index', 'business.expense-categories.index'/*, 'business.day-book-reports.index', 'business.cash-flow-reports.index','business.balance-sheet.index'*/) ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.66602 3.75H7.29715C7.96019 3.75 8.5961 4.01339 9.06493 4.48223L11.666 7.08333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.16602 11.25H1.66602" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.08268 6.25L8.74935 7.91667C9.2096 8.37692 9.2096 9.12308 8.74935 9.58333C8.28912 10.0436 7.54292 10.0436 7.08268 9.58333L5.83268 8.33333C5.11544 9.05058 3.97994 9.13125 3.16847 8.52267L2.91602 8.33333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.16602 9.16927V12.9193C4.16602 14.4906 4.16602 15.2763 4.65417 15.7644C5.14232 16.2526 5.928 16.2526 7.49935 16.2526H14.9993C16.5707 16.2526 17.3563 16.2526 17.8445 15.7644C18.3327 15.2763 18.3327 14.4906 18.3327 12.9193V10.4193C18.3327 8.84794 18.3327 8.06225 17.8445 7.5741C17.3563 7.08594 16.5707 7.08594 14.9993 7.08594H7.91602" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.7077 11.6693C12.7077 12.4747 12.0548 13.1276 11.2493 13.1276C10.4439 13.1276 9.79102 12.4747 9.79102 11.6693C9.79102 10.8639 10.4439 10.2109 11.2493 10.2109C12.0548 10.2109 12.7077 10.8639 12.7077 11.6693Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Finance & Accounts') }}</a>
                <ul>
                    {{-- @usercan('banks.read')
                    <li><a class="{{ Request::routeIs('business.banks.index') ? 'active' : '' }}" href="{{ route('business.banks.index') }}">{{ __('Bank Account') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('cashes.read')
                    <li><a class="{{ Request::routeIs('business.cashes.index') ? 'active' : '' }}" href="{{ route('business.cashes.index') }}">{{ __('Cash In Hand') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('cheques.read')
                    <li><a class="{{ Request::routeIs('business.cheques.index') ? 'active' : '' }}" href="{{ route('business.cheques.index') }}">{{ __('Cheques') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('loss-profit-history.read')
                    <li><a class="{{ Request::routeIs('business.loss-profit-history.index') ? 'active' : '' }}" href="{{ route('business.loss-profit-history.index') }}">{{ __('Profit & Loss') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('transactions.read')
                    <li><a class="{{ Request::routeIs('business.transactions.index') ? 'active' : '' }}" href="{{ route('business.transactions.index') }}">{{ __('Transactions') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('day-book-reports.read')
                    <li><a class="{{ Request::routeIs('business.day-book-reports.index') ? 'active' : '' }}" href="{{ route('business.day-book-reports.index') }}">{{ __('Day Book') }}</a></li>
                    @endusercan --}}
                    {{-- @usercan('cash-flow-reports.read')
                    <li><a class="{{ Request::routeIs('business.cash-flow-reports.index') ? 'active' : '' }}" href="{{ route('business.cash-flow-reports.index') }}">{{ __('Cash Flow') }}</a></li>
                    @endusercan --}}
                    {{-- <li><a class="{{ Request::routeIs('business.balance-sheet.index') ? 'active' : '' }}" href="{{ route('business.balance-sheet.index') }}">{{ __('Balance Sheet') }}</a></li> --}}
                    @usercan('incomes.read')
                    <li><a class="{{ Request::routeIs('business.incomes.index') ? 'active' : '' }}" href="{{ route('business.incomes.index') }}">{{ __('Income') }}</a></li>
                    @endusercan
                    @usercan('income-categories.read')
                    <li><a class="{{ Request::routeIs('business.income-categories.index') ? 'active' : '' }}" href="{{ route('business.income-categories.index') }}">{{ __('Income Category') }}</a></li>
                    @endusercan
                    @usercan('expenses.read')
                    <li><a class="{{ Request::routeIs('business.expenses.index') ? 'active' : '' }}" href="{{ route('business.expenses.index') }}">{{ __('Expenses') }}</a></li>
                    @endusercan
                    @usercan('expense-categories.read')
                    <li><a class="{{ Request::routeIs('business.expense-categories.index') ? 'active' : '' }}" href="{{ route('business.expense-categories.index') }}">{{ __('Expense Category') }}</a></li>
                    @endusercan
                </ul>
            </li>

            @usercan('subscriptions.read')
            <li class="{{ Request::routeIs('business.subscriptions.index') ? 'active' : '' }}">
                <a href="{{ route('business.subscriptions.index') }}" class="active">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5159 7.86104L12.9558 8.74825C13.0158 8.87175 13.1758 8.99017 13.3108 9.01284L14.1082 9.14642C14.6181 9.23217 14.7381 9.60517 14.3706 9.97309L13.7507 10.5982C13.6457 10.704 13.5882 10.9082 13.6207 11.0543L13.7982 11.8281C13.9382 12.4406 13.6157 12.6775 13.0783 12.3574L12.3309 11.9113C12.1959 11.8307 11.9735 11.8307 11.836 11.9113L11.0886 12.3574C10.5536 12.6775 10.2286 12.4381 10.3686 11.8281L10.5461 11.0543C10.5786 10.9082 10.5211 10.704 10.4161 10.5982L9.79622 9.97309C9.43122 9.60517 9.54872 9.23217 10.0586 9.14642L10.8561 9.01284C10.9886 8.99017 11.1486 8.87175 11.2086 8.74825L11.6485 7.86104C11.8885 7.37965 12.2784 7.37965 12.5159 7.86104Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.66602 14.166V17.0827" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.66602 2.91602V5.83268" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3343 7.39847C18.2785 6.11341 18.1223 5.27683 17.6845 4.61505C17.4327 4.23432 17.12 3.90317 16.7603 3.63658C15.7882 2.91602 14.4168 2.91602 11.6741 2.91602H8.3285C5.58577 2.91602 4.21441 2.91602 3.24231 3.63658C2.88266 3.90317 2.56985 4.23432 2.31801 4.61505C1.88034 5.27676 1.72409 6.11322 1.66831 7.39804C1.65877 7.61775 1.84802 7.78581 2.05538 7.78581C3.2102 7.78581 4.14636 8.77685 4.14636 9.99935C4.14636 11.2218 3.2102 12.2128 2.05538 12.2128C1.84802 12.2128 1.65877 12.3809 1.66831 12.6007C1.72409 13.8855 1.88034 14.7219 2.31801 15.3837C2.56985 15.7643 2.88266 16.0955 3.24231 16.3621C4.21441 17.0827 5.58577 17.0827 8.32851 17.0827H11.6741C14.4168 17.0827 15.7882 17.0827 16.7603 16.3621C17.12 16.0955 17.4327 15.7643 17.6845 15.3837C18.1223 14.7218 18.2785 13.8853 18.3343 12.6003V7.39847Z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('Subscriptions') }}
                </a>
            </li>
            @endusercan

            {{-- <li class="dropdown {{ Request::routeIs(/*'business.commissions.index',*/'business.sale-commissions.index') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <img src="{{ asset('assets/images/sidebar/cash_and_bank.svg') }}">
                    </span>
                    {{ __('Sale Commission') }}</a>
                <ul>
                    @usercan('commissions.read')
                    <li><a class="{{ Request::routeIs('business.commissions.index') ? 'active' : '' }}" href="{{ route('business.commissions.index') }}">{{ __('Set Commissions') }}</a></li>
                    @endusercan
                    @usercan('sale-commissions.read')
                    <li><a class="{{ Request::routeIs('business.sale-commissions.index') ? 'active' : '' }}" href="{{ route('business.sale-commissions.index') }}">{{ __('Sale Commission') }}</a></li>
                    @endusercan
                </ul>
            </li> --}}

            @if (moduleCheck('HrmAddon'))
              @usercanany(['department.read', 'designations.read', 'shifts.read', 'employees.read', 'leave-types.read', 'leaves.read', 'holidays.read', 'attendances.read', 'payrolls.read', 'attendance-reports.read', 'payroll-reports.read', 'leave-reports.read'])
                <li class="dropdown {{ Request::routeIs('hrm.department.index', 'hrm.designations.index', 'hrm.shifts.index', 'hrm.employees.index', 'hrm.employees.create', 'hrm.employees.edit', 'hrm.leave-types.index', 'hrm.leaves.index', 'hrm.holidays.index', 'hrm.attendances.index', 'hrm.payrolls.index', 'hrm.attendance-reports.index','hrm.leave-reports.index','hrm.payroll-reports.index') ? 'active' : '' }}">
                    <a class="position-relative" href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.08398 13.7494C7.66618 12.743 8.75437 12.0658 10.0006 12.0658C11.247 12.0658 12.3351 12.743 12.9173 13.7494M11.6674 8.33268C11.6674 9.25318 10.9211 9.99937 10.0007 9.99937C9.0802 9.99937 8.33401 9.25318 8.33401 8.33268C8.33401 7.41221 9.0802 6.66602 10.0007 6.66602C10.9211 6.66602 11.6674 7.41221 11.6674 8.33268Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                                <path d="M18.3327 11.6397V8.36165C15.9521 8.36165 14.4041 5.77599 15.6093 3.72307L12.7226 2.08407C11.5025 4.16236 8.49752 4.16227 7.27737 2.08398L4.39062 3.72299C5.5959 5.77594 4.04662 8.36165 1.66602 8.36165V11.6397C4.04658 11.6397 5.59456 14.2254 4.38931 16.2783L7.27606 17.9173C8.49677 15.838 11.5032 15.8379 12.7239 17.9172L15.6107 16.2782C14.4055 14.2253 15.9522 11.6397 18.3327 11.6397Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('HRM') }}
                        @if (env('DEMO_MODE'))
                        <sup class="badge bg-warning position-absolute side-bar-addon-3">{{__('Add-On')}}</sup>
                        @endif
                    </a>
                    @usercan('department.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.department.index') ? 'active' : '' }}"
                                href="{{ route('hrm.department.index') }}">{{ __('Department') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercan('designations.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.designations.index') ? 'active' : '' }}"
                                href="{{ route('hrm.designations.index') }}">{{ __('Designation') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercan('shifts.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.shifts.index') ? 'active' : '' }}"
                                href="{{ route('hrm.shifts.index') }}">{{ __('Shift') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercan('employees.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.employees.index', 'hrm.employees.create', 'hrm.employees.edit') ? 'active' : '' }}"
                                href="{{ route('hrm.employees.index') }}">{{ __('Employee') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercanany(['leave-types.read', 'leaves.read'])
                    <ul>
                        <li class="dropdown {{ Request::routeIs('hrm.leave-types.index', 'hrm.leaves.index') ? 'active' : '' }}">
                            <a href="">{{ __('Leave Request') }}</a>
                            <ul>
                                @usercan('leave-types.read')
                                <li>
                                    <a class="{{ Request::routeIs('hrm.leave-types.index') ? 'active' : '' }}"
                                        href="{{ route('hrm.leave-types.index') }}">{{ __('Leave Type') }}</a>
                                </li>
                                @endusercan
                                @usercan('leaves.read')
                                <li>
                                    <a class="{{ Request::routeIs('hrm.leaves.index') ? 'active' : '' }}"
                                        href="{{ route('hrm.leaves.index') }}">{{ __('Leave') }}</a>
                                </li>
                                @endusercan
                            </ul>
                        </li>
                    </ul>
                    @endusercanany

                    @usercan('holidays.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.holidays.index') ? 'active' : '' }}"
                                href="{{ route('hrm.holidays.index') }}">{{ __('Holiday') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercan('attendances.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.attendances.index') ? 'active' : '' }}"
                                href="{{ route('hrm.attendances.index') }}">{{ __('Attendance') }}</a>
                        </li>
                    </ul>
                    @endusercan
                    @usercan('payrolls.read')
                    <ul>
                        <li>
                            <a class="{{ Request::routeIs('hrm.payrolls.index') ? 'active' : '' }}"
                                href="{{ route('hrm.payrolls.index') }}">{{ __('Payroll') }}</a>
                        </li>
                    </ul>
                    @endusercan

                    @usercanany(['attendance-reports.read', 'payroll-reports.read', 'leave-reports.read'])
                    <ul>
                        <li class="dropdown {{ Request::routeIs('hrm.attendance-reports.index', 'hrm.payroll-reports.index','hrm.leave-reports.index') ? 'active' : '' }}">
                            <a href="">{{ __('Reports') }}</a>
                            <ul>
                                @usercan('attendance-reports.read')
                                <li>
                                    <a class="{{ Request::routeIs('hrm.attendance-reports.index') ? 'active' : '' }}"
                                        href="{{ route('hrm.attendance-reports.index') }}">{{ __('Attendance') }}</a>
                                </li>
                                @endusercan
                                @usercan('payroll-reports.read')
                                <li>
                                    <a class="{{ Request::routeIs('hrm.payroll-reports.index') ? 'active' : '' }}"
                                        href="{{ route('hrm.payroll-reports.index') }}">{{ __('Payroll') }}</a>
                                </li>
                                @endusercan
                                @usercan('leave-reports.read')
                                <li>
                                    <a class="{{ Request::routeIs('hrm.leave-reports.index') ? 'active' : '' }}" href="{{ route('hrm.leave-reports.index') }}">{{ __('Leave') }}</a>
                                </li>
                                @endusercan
                            </ul>
                        </li>
                    </ul>
                    @endusercanany
                </li>
                @endusercanany
            @endif

            @usercanany(['sale-reports.read', 'sale-return-reports.read', 'purchase-reports.read', 'purchase-return-reports.read', 'vat-reports.read', 'income-reports.read', 'expense-reports.read', 'loss-profits-details.read', 'stock-reports.read', 'due-reports.read', 'supplier-due-reports.read', 'loss-profit-reports.read', 'transaction-history-reports.read', 'subscription-reports.read', 'expired-product-reports.read'])
                <li class="dropdown {{ Request::routeIs('business.income-reports.index', 'business.expense-reports.index', 'business.stock-reports.index', 'business.sale-reports.index', 'business.purchase-reports.index', 'business.due-reports.index', 'business.sale-return-reports.index', 'business.purchase-return-reports.index', 'business.supplier-due-reports.index', 'business.transaction-history-reports.index', 'business.subscription-reports.index', 'business.expired-product-reports.index','business.vat-reports.index', 'business.loss-profit-reports.details', 'business.custom-reports.show'/*, 'business.top-product-reports.index', 'business.product-loss-profit-reports.index', 'business.discount-product-reports.index', 'business.combo-product-reports.index', 'business.product-purchase-reports.index', 'business.product-sale-reports.index', 'business.bill-wise-profits.index','business.loss-profit-history-reports.index', 'business.product-sale-history-reports.index', 'business.product-sale-history-reports.show', 'business.top-customer-reports.index', 'business.top-supplier-reports.index', 'business.product-purchase-history-reports.index', 'business.product-purchase-history-reports.show'*/) ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.66602 13.3327H9.99931M6.66602 9.16602H13.3326" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                                <path d="M6.25065 2.91602C4.95415 2.95491 4.18116 3.09922 3.6463 3.63457C2.91406 4.36749 2.91406 5.5471 2.91406 7.90632V13.328C2.91406 15.6873 2.91406 16.8668 3.6463 17.5998C4.37853 18.3327 5.55705 18.3327 7.91406 18.3327H12.0807C14.4377 18.3327 15.6162 18.3327 16.3485 17.5998C17.0807 16.8668 17.0807 15.6873 17.0807 13.328V7.90632C17.0807 5.5471 17.0807 4.36749 16.3485 3.63458C15.8137 3.09922 15.0407 2.95491 13.7442 2.91602" stroke="currentColor" stroke-width="1.25"/>
                                <path d="M6.24609 3.12435C6.24609 2.31893 6.89902 1.66602 7.70443 1.66602H12.2878C13.0932 1.66602 13.7461 2.31893 13.7461 3.12435C13.7461 3.92977 13.0932 4.58268 12.2878 4.58268H7.70443C6.89902 4.58268 6.24609 3.92977 6.24609 3.12435Z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        {{ __('Reports') }}</a>
                    <ul>
                        @usercan('sale-reports.read')
                        <li><a class="{{ Request::routeIs('business.sale-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.sale-reports.index') }}">{{ __('Sale') }}</a></li>
                        @endusercan

                        @usercan('sale-return-reports.read')
                        <li><a class="{{ Request::routeIs('business.sale-return-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.sale-return-reports.index') }}">{{ __('Sale Return') }}</a>
                        </li>
                        @endusercan

                        @usercan('purchase-reports.read')
                        <li><a class="{{ Request::routeIs('business.purchase-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.purchase-reports.index') }}">{{ __('Purchase') }}</a>
                        </li>
                        @endusercan

                        @usercan('purchase-return-reports.read')
                        <li><a class="{{ Request::routeIs('business.purchase-return-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.purchase-return-reports.index') }}">{{ __('Purchase Return') }}</a>
                        </li>
                        @endusercan

                        @usercan('vat-reports.read')
                        <li><a class="{{ Request::routeIs('business.vat-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.vat-reports.index') }}">{{ __('Tax Report') }}</a>
                        </li>
                        @endusercan

                        @usercan('income-reports.read')
                        <li><a class="{{ Request::routeIs('business.income-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.income-reports.index') }}">{{ __('Income') }}</a></li>
                        @endusercan

                        @usercan('expense-reports.read')
                        <li><a class="{{ Request::routeIs('business.expense-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.expense-reports.index') }}">{{ __('Expense') }}</a>
                        </li>
                        @endusercan


                        @usercan('stock-reports.read')
                        <li><a class="{{ Request::routeIs('business.stock-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.stock-reports.index') }}">{{ __('Current Stock') }}</a>
                        </li>
                        @endusercan

                        @usercan('due-reports.read')
                        <li><a class="{{ Request::routeIs('business.due-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.due-reports.index') }}">{{ __('Customer Due') }}</a></li>
                        @endusercan

                        @usercan('supplier-due-reports.read')
                        <li><a class="{{ Request::routeIs('business.supplier-due-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.supplier-due-reports.index') }}">{{ __('Supplier Due') }}</a>
                        </li>
                        @endusercan

                        {{-- @usercan('bill-wise-profits.read')
                        <li>
                            <a class="{{ Request::routeIs('business.bill-wise-profits.index') ? 'active' : '' }}"
                                href="{{ route('business.bill-wise-profits.index') }}">{{ __('Bill Wise Profit & Loss') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('product-loss-profit-reports.read')
                        <li><a class="{{ Request::routeIs('business.product-loss-profit-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.product-loss-profit-reports.index') }}">{{ __('Product Wise Profit & Loss') }}</a>
                        </li>
                        @endusercan --}}

                        @usercan('transaction-history-reports.read')
                        <li><a class="{{ Request::routeIs('business.transaction-history-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.transaction-history-reports.index') }}">{{ __('Due Transaction') }}</a>
                        </li>
                        @endusercan

                        @usercan('subscription-reports.read')
                        <li><a class="{{ Request::routeIs('business.subscription-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.subscription-reports.index') }}">{{ __('Subscription Report') }}</a>
                        </li>
                        @endusercan

                        {{-- @usercan('top-customers-reports.read')
                        <li>
                            <a class="{{ Request::routeIs('business.top-customer-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.top-customer-reports.index') }}">{{ __('Top 5 Customer') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('top-suppliers-reports.read')
                        <li>
                            <a class="{{ Request::routeIs('business.top-supplier-reports.index') ? 'active' : '' }}"
                                href="{{ route('business.top-supplier-reports.index') }}">{{ __('Top 5 Supplier') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('top-product-reports.read')
                        <li>
                            <a class="{{ Request::routeIs('business.top-product-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.top-product-reports.index') }}">{{ __('Top 5 Product') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('combo-product-reports.read')
                        <li><a class="{{ Request::routeIs('business.combo-product-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.combo-product-reports.index') }}">{{ __('Combo Product') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('discount-product-reports.read')
                        <li><a class="{{ Request::routeIs('business.discount-product-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.discount-product-reports.index') }}">{{ __('Discount Product') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('product-purchase-reports.read')
                        <li><a class="{{ Request::routeIs('business.product-purchase-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.product-purchase-reports.index') }}">{{ __('Product Wise Purchase') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('product-sale-reports.read')
                        <li><a class="{{ Request::routeIs('business.product-sale-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.product-sale-reports.index') }}">{{ __('Product Wise Sale') }}</a>
                        </li>
                        @endusercan --}}

                        @usercan('expired-product-reports.read')
                        <li><a class="{{ Request::routeIs('business.expired-product-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.expired-product-reports.index') }}">{{ __('Expired Product') }}</a>
                        </li>
                        @endusercan

                        {{-- @usercan('loss-profit-history-reports.read')
                        <li><a class="{{ Request::routeIs('business.loss-profit-history-reports.index') ? 'active' : '' }}"
                            href="{{ route('business.loss-profit-history-reports.index') }}">{{ __('Loss Profit History') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('product-sale-history-reports.read')
                        <li><a class="{{ Request::routeIs('business.product-sale-history-reports.index', 'business.product-sale-history-reports.show') ? 'active' : '' }}"
                            href="{{ route('business.product-sale-history-reports.index') }}">{{ __('Product Sale History') }}</a>
                        </li>
                        @endusercan --}}

                        {{-- @usercan('product-purchase-history-reports.read')
                        <li><a class="{{ Request::routeIs('business.product-purchase-history-reports.index', 'business.product-purchase-history-reports.show') ? 'active' : '' }}"
                            href="{{ route('business.product-purchase-history-reports.index') }}">{{ __('Product Purchase History') }}</a>
                        </li>
                        @endusercan --}}

                        @if (moduleCheck('HrmAddon'))
                            {{-- HRM Reports are already in the HRM dropdown menu above, no need to duplicate here
                            @usercan('attendance-reports.read')
                            <li>
                                <a class="{{ Request::routeIs('hrm.attendance-reports.index') ? 'active' : '' }}"
                                    href="{{ route('hrm.attendance-reports.index') }}">{{ __('Attendance') }}</a>
                            </li>
                            @endusercan
                            @usercan('payroll-reports.read')
                            <li>
                                <a class="{{ Request::routeIs('hrm.payroll-reports.index') ? 'active' : '' }}"
                                    href="{{ route('hrm.payroll-reports.index') }}">{{ __('Payroll') }}</a>
                            </li>
                            @endusercan
                            @usercan('leave-reports.read')
                            <li>
                                <a class="{{ Request::routeIs('hrm.leave-reports.index') ? 'active' : '' }}"
                                    href="{{ route('hrm.leave-reports.index') }}">{{ __('Leave') }}</a>
                            </li>
                            @endusercan
                            --}}
                        @endif

                        @foreach (custom_reports() as $custom_report)
                        <li>
                            <a class="{{ $custom_report->slug == request()->route('custom_report') ? 'active' : '' }}" href="{{ route('business.custom-reports.show', $custom_report->slug) }}">
                                {{ $custom_report->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
            @endusercanany

            {{-- <li class="dropdown {{ Request::routeIs(/*'business.customer-ledger.index', 'business.supplier-ledger.index', 'business.top-customers.index', 'business.top-suppliers.index', 'business.party-loss-profit.index', 'business.customer-ledger.show', 'business.supplier-ledger.show'*/) ? 'active' : '' }}">
                    <a href="#">
                        <span class="sidebar-icon">
                            <img src="{{ asset('assets/images/icons/party-report.png') }}">
                        </span>
                        {{ __('Party Reports') }}</a>
                    <ul>
                        @usercan('customer-ledger.read')
                        <li>
                            <a class="{{ Request::routeIs('business.customer-ledger.index','business.customer-ledger.show') ? 'active' : '' }}"
                                href="{{ route('business.customer-ledger.index') }}">{{ __('Customer Ledger') }}
                            </a>
                        </li>
                        @endusercan

                        @usercan('supplier-ledger.read')
                        <li>
                            <a class="{{ Request::routeIs('business.supplier-ledger.index', 'business.supplier-ledger.show') ? 'active' : '' }}"
                                href="{{ route('business.supplier-ledger.index') }}">{{ __('Supplier Ledger') }}
                            </a>
                        </li>
                        @endusercan

                        @usercan('party-loss-profit.read')
                        <li>
                            <a class="{{ Request::routeIs('business.party-loss-profit.index') ? 'active' : '' }}"
                                href="{{ route('business.party-loss-profit.index') }}">{{ __('Party Profit & Loss') }}
                            </a>
                        </li>
                        @endusercan

                        @usercan('top-customers-reports.read')
                        <li>
                            <a class="{{ Request::routeIs('business.top-customers.index') ? 'active' : '' }}"
                                href="{{ route('business.top-customers.index') }}">{{ __('Top 5 Customer') }}
                            </a>
                        </li>
                        @endusercan

                        @usercan('top-suppliers-reports.read')
                        <li>
                            <a class="{{ Request::routeIs('business.top-suppliers.index') ? 'active' : '' }}"
                                href="{{ route('business.top-suppliers.index') }}">{{ __('Top 5 Supplier') }}
                            </a>
                        </li>
                        @endusercan
                    </ul>
                </li> --}}

            {{-- @if (moduleCheck('CustomReportsAddon'))
                @usercanany(['custom-reports.read', 'custom-reports.create'])
                    <li class="dropdown {{ Request::routeIs('business.custom-reports.index', 'business.custom-reports.create', 'business.custom-reports.edit') ? 'active' : '' }}">
                        <a href="#">
                            <span class="sidebar-icon">
                                <img src="{{ asset('assets/images/sidebar/custom_report.svg') }}">
                            </span>
                            {{ __('Custom Reports') }}</a>
                        <ul>
                            @usercan('custom-reports.create')
                            <li>
                                <a class="{{ Request::routeIs('business.custom-reports.create') ? 'active' : '' }}" href="{{ route('business.custom-reports.create') }}">{{ __('Add New') }}</a>
                            </li>
                            @endusercan
                            @usercan('custom-reports.read')
                            <li>
                                <a class="{{ Request::routeIs('business.custom-reports.index') ? 'active' : '' }}" href="{{ route('business.custom-reports.index') }}">{{ __('View List') }}</a>
                            </li>
                            @endusercan
                        </ul>
                    </li>
                @endusercanany
            @endif --}}

            @if (moduleCheck('CustomDomainAddon'))
             @usercanany(['domains.read', 'domains.read'])
                <li class="{{ Request::routeIs('business.domains.index') ? 'active' : '' }}">
                    <a href="{{ route('business.domains.index') }}" class="active">
                        <span class="sidebar-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.0343 6.47825H17.183C15.8567 3.78589 13.1445 2.04377 10.1354 1.98438H9.87802C6.8689 2.04377 4.15674 3.76609 2.83035 6.47825H1.97909C1.306 6.47825 0.771484 7.01277 0.771484 7.68586V12.2985C0.771484 12.9716 1.306 13.5061 1.97909 13.5061H2.83035C4.15674 16.1985 6.8689 17.9406 9.87802 18H10.1552C13.1643 17.9406 15.8765 16.2183 17.2028 13.5061H18.0541C18.7272 13.5061 19.2617 12.9716 19.2617 12.2985V7.70566C19.2617 7.03256 18.7074 6.49805 18.0343 6.47825ZM6.45317 6.47825C7.10647 4.35999 8.31407 2.93462 9.66026 2.71686V6.47825H6.45317ZM10.3531 2.71686C11.6795 2.93462 12.8871 4.35999 13.5602 6.47825H10.3531V2.71686ZM14.2729 6.47825C13.8572 5.05288 13.2237 3.88487 12.4516 3.11279C14.1195 3.69185 15.5746 4.91925 16.411 6.47825H14.2729ZM7.56179 3.11279C6.78972 3.88487 6.15622 5.05288 5.74049 6.47825H3.58263C4.45369 4.9143 5.85927 3.7265 7.56179 3.11279ZM3.58263 13.5061H5.72069C6.13642 14.9513 6.76992 16.0995 7.542 16.8716C5.85927 16.2777 4.45369 15.0899 3.58263 13.5061ZM10.3531 17.2873V13.5259H13.5602C12.8871 15.6442 11.6795 17.0696 10.3531 17.2873ZM9.66026 17.2873C8.33387 17.0696 7.12626 15.6442 6.45317 13.5259H9.66026V17.2873ZM16.1536 13.9219C15.2677 15.2779 13.9562 16.3717 12.412 16.8914C13.1841 16.1193 13.8176 14.9513 14.2333 13.5259H16.3714C16.3714 13.5259 16.2476 13.7734 16.1536 13.9219ZM18.5688 12.2985C18.5688 12.5757 18.3313 12.8132 18.0541 12.8132H1.95929C1.68214 12.8132 1.44458 12.5757 1.44458 12.2985V7.70566C1.44458 7.4285 1.68214 7.19094 1.95929 7.19094H18.0541C18.3313 7.19094 18.5688 7.4285 18.5688 7.70566V12.2985Z" fill="currentColor"/>
                                <path d="M6.65198 8.47784C6.47381 8.39865 6.27584 8.47784 6.19665 8.65601L5.44437 10.2793L4.92966 8.97276C4.87027 8.83418 4.75149 8.75499 4.61291 8.75499C4.47433 8.75499 4.33575 8.83418 4.29616 8.97276L3.78144 10.2793L3.04896 8.65601C2.96977 8.47784 2.7718 8.39865 2.59363 8.47784C2.41546 8.55703 2.33627 8.75499 2.41546 8.93317L3.48449 11.3088C3.54388 11.4276 3.66266 11.5068 3.80124 11.5068C3.93982 11.5068 4.0586 11.4078 4.11799 11.289L4.61291 10.0418L5.10783 11.289C5.16722 11.4276 5.286 11.5068 5.42458 11.5068C5.56316 11.5068 5.68194 11.4276 5.74133 11.3088L6.81035 8.93317C6.88954 8.77479 6.83015 8.55703 6.65198 8.47784ZM12.0565 8.47784C11.8783 8.39865 11.6804 8.47784 11.6012 8.65601L10.8687 10.2793L10.354 8.97276C10.2946 8.83418 10.1758 8.75499 10.0372 8.75499C9.89866 8.75499 9.76008 8.83418 9.72049 8.97276L9.20577 10.2793L8.47329 8.65601C8.3941 8.47784 8.19613 8.39865 8.01796 8.47784C7.83979 8.55703 7.7606 8.75499 7.83979 8.93317L8.90882 11.3088C8.96821 11.4276 9.08699 11.5068 9.22557 11.5068C9.36414 11.5068 9.48292 11.4078 9.54232 11.289L10.0372 10.0418L10.5322 11.289C10.5915 11.4276 10.7103 11.5068 10.8489 11.5068C10.9875 11.5068 11.1063 11.4276 11.1657 11.3088L12.2347 8.93317C12.3139 8.77479 12.2347 8.55703 12.0565 8.47784ZM17.4808 8.47784C17.3027 8.39865 17.1047 8.47784 17.0255 8.65601L16.293 10.2793L15.7783 8.97276C15.7189 8.83418 15.6001 8.75499 15.4616 8.75499C15.323 8.75499 15.1844 8.83418 15.1448 8.97276L14.6301 10.2793L13.8976 8.65601C13.8184 8.47784 13.6205 8.39865 13.4423 8.47784C13.2641 8.55703 13.1849 8.75499 13.2641 8.93317L14.3331 11.3088C14.3925 11.4276 14.5113 11.5068 14.6499 11.5068C14.7885 11.5068 14.9073 11.4078 14.9666 11.289L15.4616 10.0418L15.9565 11.289C16.0159 11.4276 16.1347 11.5068 16.2732 11.5068C16.4118 11.5068 16.5306 11.4276 16.59 11.3088L17.659 8.93317C17.7382 8.77479 17.659 8.55703 17.4808 8.47784Z" fill="currentColor"/>
                            </svg>
                        </span>
                        {{ __('My Domains') }}
                    </a>
                </li>
             @endusercanany
            @endif

            @if (moduleCheck('MarketingAddon'))
            <li class="dropdown {{ Request::routeIs('business.sms-templates.index', 'business.sms-gateways.index', 'business.sms-gateways.create', 'business.sms-gateways.edit', 'business.devices.index', 'business.devices.create', 'business.devices.edit') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 18.333C9.31817 18.333 8.66683 18.0578 7.36411 17.5076C4.12137 16.1378 2.5 15.4528 2.5 14.3008C2.5 13.9782 2.5 8.38676 2.5 5.83301M10 18.333C10.6818 18.333 11.3332 18.0578 12.6359 17.5076C15.8787 16.1378 17.5 15.4528 17.5 14.3008V5.83301M10 18.333V9.46201" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.93827 8.07647L4.50393 6.89853C3.16797 6.25208 2.5 5.92885 2.5 5.41699C2.5 4.90513 3.16797 4.58191 4.50393 3.93545L6.93827 2.75751C8.44067 2.0305 9.19192 1.66699 10 1.66699C10.8081 1.66699 11.5593 2.03049 13.0617 2.75751L15.4961 3.93545C16.832 4.58191 17.5 4.90513 17.5 5.41699C17.5 5.92885 16.832 6.25208 15.4961 6.89853L13.0617 8.07647C11.5593 8.80349 10.8081 9.16699 10 9.16699C9.19192 9.16699 8.44067 8.80349 6.93827 8.07647Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 10L6.66667 10.8333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.1673 3.33398L5.83398 7.50065" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ __('SMS Marketing') }}
                </a>
                <ul>
                    <li>
                        <a class="{{ Request::routeIs('business.sms-templates.index') ? 'active' : '' }}" href="{{ route('business.sms-templates.index') }}">{{ __('SMS Template') }}</a>
                    </li>
                    <li>
                        <a class="{{ Request::routeIs('business.sms-gateways.index', 'business.sms-gateways.create', 'business.sms-gateways.edit') ? 'active' : '' }}" href="{{ route('business.sms-gateways.index') }}">{{ __('API Gateway') }}</a>
                    </li>
                    <li>
                        <a class="{{ Request::routeIs('business.devices.index', 'business.devices.create', 'business.devices.edit') ? 'active' : '' }}" href="{{ route('business.devices.index') }}">{{ __('Android Gateway') }}</a>
                    </li>
                </ul>
            </li>
            @endif

            @usercanany(['manage-settings.read', 'zatca-settings-read'])
            <li class="dropdown {{ Request::routeIs('business.manage-settings.index', 'business.currencies.index', 'business.currencies.create', 'business.currencies.edit', 'business.notifications.index','business.settings.index', 'business.sms-gateway-settings.index', 'business.zatca.index', 'business.moyasar.index') ? 'active' : '' }}">
                <a href="#">
                    <span class="sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6.24997C7.93205 6.24997 6.25005 7.93197 6.25005 9.99997C6.25005 12.068 7.93205 13.75 10 13.75C12.068 13.75 13.75 12.068 13.75 9.99997C13.75 7.93197 12.068 6.24997 10 6.24997ZM10 12.25C8.75905 12.25 7.75005 11.241 7.75005 9.99997C7.75005 8.75897 8.75905 7.74997 10 7.74997C11.241 7.74997 12.25 8.75897 12.25 9.99997C12.25 11.241 11.241 12.25 10 12.25ZM19.2081 11.953C18.5141 11.551 18.082 10.803 18.081 9.99997C18.08 9.19897 18.5091 8.45198 19.2121 8.04498C19.7271 7.74598 19.9031 7.08296 19.6051 6.56696L17.9331 3.68097C17.6351 3.16597 16.972 2.98898 16.456 3.28598C15.757 3.68898 14.8881 3.68898 14.1871 3.28198C13.4961 2.88098 13.0661 2.13598 13.0661 1.33698C13.0661 0.737975 12.578 0.250977 11.979 0.250977H8.02403C7.42403 0.250977 6.93706 0.737975 6.93706 1.33698C6.93706 2.13598 6.50704 2.88097 5.81404 3.28397C5.11504 3.68897 4.24705 3.68996 3.54805 3.28696C3.03105 2.98896 2.36906 3.16698 2.07106 3.68198L0.397049 6.57098C0.0990486 7.08598 0.276035 7.74796 0.796035 8.04996C1.48904 8.45096 1.92105 9.19796 1.92305 9.99896C1.92505 10.801 1.49504 11.55 0.793045 11.957C0.543045 12.102 0.363047 12.335 0.289047 12.615C0.215047 12.894 0.253056 13.185 0.398056 13.436L2.06905 16.32C2.36705 16.836 3.03005 17.015 3.54805 16.716C4.24705 16.313 5.11405 16.314 5.80305 16.713L5.80504 16.714C5.80804 16.716 5.81105 16.718 5.81505 16.72C6.50605 17.121 6.93504 17.866 6.93404 18.666C6.93404 19.265 7.42103 19.752 8.02003 19.752H11.979C12.578 19.752 13.065 19.265 13.065 18.667C13.065 17.867 13.495 17.122 14.189 16.719C14.887 16.314 15.755 16.312 16.455 16.716C16.971 17.014 17.6331 16.837 17.9321 16.322L19.606 13.433C19.903 12.916 19.7261 12.253 19.2081 11.953ZM16.831 15.227C15.741 14.752 14.476 14.817 13.434 15.42C12.401 16.019 11.7191 17.078 11.5871 18.25H8.41005C8.28005 17.078 7.59603 16.017 6.56303 15.419C5.52303 14.816 4.25605 14.752 3.16905 15.227L1.89305 13.024C2.84805 12.321 3.42504 11.193 3.42104 9.99298C3.41804 8.80098 2.84204 7.68097 1.89204 6.97797L3.16905 4.77396C4.25705 5.24796 5.52405 5.18396 6.56605 4.57996C7.59805 3.98196 8.28003 2.92198 8.41203 1.75098H11.5871C11.7181 2.92298 12.4011 3.98197 13.4361 4.58197C14.475 5.18497 15.742 5.24896 16.831 4.77496L18.108 6.97797C17.155 7.67997 16.579 8.80597 16.581 10.004C16.582 11.198 17.1581 12.32 18.1091 13.025L16.831 15.227Z" fill="currentColor" />
                        </svg>
                    </span>
                    {{ __('Settings') }}
                </a>
                <ul>
                    @usercan('manage-settings.read')
                    <li>
                        <a class="{{ Request::routeIs('business.manage-settings.index') ? 'active' : '' }}" href="{{ route('business.manage-settings.index') }}">{{ __('General Settings') }}</a>
                    </li>
                    @endusercan
                    
                    @usercan('zatca-settings-read')
                    <li>
                        <a class="{{ Request::routeIs('business.zatca.index') ? 'active' : '' }}" href="{{ route('business.zatca.index') }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i>{{ __('ZATCA Settings') }}
                        </a>
                    </li>
                    @endusercan
                    
                    @usercan('moyasar-settings-read')
                    <li>
                        <a class="{{ Request::routeIs('business.moyasar.index') ? 'active' : '' }}" href="{{ route('business.moyasar.index') }}">
                            <i class="fas fa-credit-card me-2"></i>{{ __('Moyasar Settings') }}
                        </a>
                    </li>
                    @endusercan
                </ul>
            </li>
            @endusercanany

            @usercan('download-apk.read')
            <li>
                <a href="{{ get_option('general')['app_link'] ?? '' }}" target="_blank" class="active">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path
                                d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7 .1 5.4 .2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zm-132.9 88.7L299.3 420.7c-6.2 6.2-16.4 6.2-22.6 0L171.3 315.3c-10.1-10.1-2.9-27.3 11.3-27.3H248V176c0-8.8 7.2-16 16-16h48c8.8 0 16 7.2 16 16v112h65.4c14.2 0 21.4 17.2 11.3 27.3z" />
                        </svg>
                    </span>
                    {{ __('Download Apk') }}
                </a>
            </li>
            @endusercan

            @usercan('subscriptions.read')
            <li>
                <div class="sub-plan">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 6.95259L18.0466 9.07413L18.401 9.7829C18.9054 10.7931 18.4193 12.0406 17.1132 12.4037C17.0967 12.4092 15.2877 12.8948 14.7383 11.2492L14.2088 9.65856L15.7156 8.15104L11.9973 2.57422L8.27889 8.15104L9.78575 9.65856L9.25622 11.2492C8.91349 12.276 8.17795 12.6221 6.87438 12.3742C6.73204 12.3048 5.98867 12.2726 5.5681 11.357C5.33597 10.8529 5.34558 10.2795 5.59351 9.7829L5.94722 9.07478L0 6.95189L2.6534 17.2045H8.89291L11.9973 14.1001L15.1016 17.2045H21.3425L24 6.95259Z" fill="currentColor"/>
                        <path d="M10.1766 17.9063L11.9949 16.0879L13.8134 17.9063L11.9949 19.7246L10.1766 17.9063ZM8.89383 18.6102H2.85547V21.4233H11.707L8.89383 18.6102ZM21.1409 18.6102H15.1025L12.2894 21.4233H21.1409V18.6102Z" fill="currentColor"/>
                    </svg>
                </div>
            </li>
            @endusercan

            @usercan('subscriptions.read')
            <li>
                <div class="lg-sub-plan">
                    <div id="sidebar_plan" class=" sidebar-free-plan d-flex align-items-center justify-content-between p-3 flex-column">
                        <div class="text-center">
                            @if (plan_data() ?? false)

                                <h3>
                                    {{ plan_data()['plan']['subscriptionName'] ?? '' }}
                                </h3>
                                <h5>
                                    {{ __('Expired') }}: {{ formatted_date(plan_data()['will_expire'] ?? '') }}
                                </h5>
                                @else
                                <h3>{{ __('No Active Plan') }}</h3>
                                <h5>{{ __('Please subscribe to a plan') }}</h5>
                            @endif

                        </div>
                        <a href="{{ route('business.subscriptions.index') }}" class="btn upgrate-btn fw-bold">{{ __('Upgrade Now') }}</a>
                    </div>
                </div>
            </li>
            @endusercan

        </ul>
    </div>
</nav>
