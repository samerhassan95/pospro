<header class="main-header-section sticky-top d-print-none">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between" style="flex-wrap: nowrap;">
        <div class="bg-white d-flex align-items-center">
            <div class="sidebar-opner menu-opener"><i class="fal fa-bars" aria-hidden="true"></i></div>
  
      
              <a href="{{ route('admin.dashboard.index') }}" class="pos-logo">
            <img src="{{ asset('assets/images/Logo.png') }}" alt="Logo" class="sidebar-logo-img">
            <span class="sidebar-logo-text"><span class="bytes-text">Bytes</span> Pos</span>
        </a>
        </div>

        <div class=" header-right d-flex justify-content-center align-items-center">
            <a href="{{ route('business.sales.create') }}" class="pos-add-expense-btn">
              <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.599 9.11326C17.8868 9.40104 17.8868 9.86761 17.599 10.1554L15.438 12.3165C15.1502 12.6042 14.6836 12.6042 14.3958 12.3165C14.1081 12.0287 14.1081 11.5621 14.3958 11.2743L16.5569 9.11326C16.8447 8.82548 17.3113 8.82548 17.599 9.11326Z" fill="currentColor"/>
                    <path d="M13.5936 13.0967C13.8814 13.3845 13.8814 13.8511 13.5936 14.1388L12.632 15.1004C12.3442 15.3882 11.8776 15.3882 11.5898 15.1004C11.3021 14.8127 11.3021 14.3461 11.5898 14.0583L12.5514 13.0967C12.8392 12.8089 13.3058 12.8089 13.5936 13.0967Z" fill="currentColor"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.53718 1.36998C10.3638 -0.456662 13.3254 -0.456661 15.152 1.36998L19.7545 5.97241C21.5811 7.79905 21.5811 10.7606 19.7545 12.5873L12.6911 19.6507H18.4225C18.8295 19.6507 19.1594 19.9806 19.1594 20.3876C19.1594 20.7945 18.8295 21.1245 18.4225 21.1245H2.70197C2.29499 21.1245 1.96507 20.7945 1.96507 20.3876C1.96507 19.9806 2.29499 19.6507 2.70197 19.6507H5.86859L1.36998 15.152C-0.456661 13.3254 -0.456661 10.3638 1.36998 8.53718L8.53718 1.36998ZM9.57931 2.41212C10.6907 1.30076 12.4154 1.17663 13.6638 2.03973L2.0397 13.6637C1.17664 12.4154 1.30077 10.6907 2.41212 9.57931L9.57931 2.41212ZM7.01454 18.7123L3.04496 14.7428L14.7428 3.04499L18.7123 7.01454C19.9634 8.26563 19.9634 10.2941 18.7123 11.5451L11.5451 18.7123C10.2941 19.9634 8.26563 19.9634 7.01454 18.7123Z" fill="currentColor"/>
                </svg>  POS
                </a>
                      <a target="_blank" class="text-custom-primary view-website" href="{{ route('home') }}">
                <!-- {{ __('View Website') }} -->
<svg width="28" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-32">
  <path d="M21.721 12.752a9.711 9.711 0 0 0-.945-5.003 12.754 12.754 0 0 1-4.339 2.708 18.991 18.991 0 0 1-.214 4.772 17.165 17.165 0 0 0 5.498-2.477ZM14.634 15.55a17.324 17.324 0 0 0 .332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 0 0 .332 4.647 17.385 17.385 0 0 0 5.268 0ZM9.772 17.119a18.963 18.963 0 0 0 4.456 0A17.182 17.182 0 0 1 12 21.724a17.18 17.18 0 0 1-2.228-4.605ZM7.777 15.23a18.87 18.87 0 0 1-.214-4.774 12.753 12.753 0 0 1-4.34-2.708 9.711 9.711 0 0 0-.944 5.004 17.165 17.165 0 0 0 5.498 2.477ZM21.356 14.752a9.765 9.765 0 0 1-7.478 6.817 18.64 18.64 0 0 0 1.988-4.718 18.627 18.627 0 0 0 5.49-2.098ZM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 0 0 1.988 4.718 9.765 9.765 0 0 1-7.478-6.816ZM13.878 2.43a9.755 9.755 0 0 1 6.116 3.986 11.267 11.267 0 0 1-3.746 2.504 18.63 18.63 0 0 0-2.37-6.49ZM12 2.276a17.152 17.152 0 0 1 2.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0 1 12 2.276ZM10.122 2.43a18.629 18.629 0 0 0-2.37 6.49 11.266 11.266 0 0 1-3.746-2.504 9.754 9.754 0 0 1 6.116-3.985Z" />
</svg>

          </a>






            @if (moduleCheck('MultiBranchAddon') && auth()->user()->active_branch_id)
            @php
                $branch = auth()->user()->active_branch;
            @endphp
            <a class="d-flex align-items-center gap-1 exit-branch-btn !text-custom-primary" href="javascript:void(0)" style=" color: #FF6500;" data-title="Are you sure you want to exit from {{ $branch->name ?? '' }}?" data-exit-url="{{ route('multibranch.exit-branch', $branch->id ?? null) }}">


                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #FF6500;">
<path fill-rule="evenodd" clip-rule="evenodd" d="M3 9.75C3.55229 9.75 4 10.1977 4 10.75V15.25C4 16.6925 4.00213 17.6737 4.10092 18.4086C4.19585 19.1146 4.36322 19.4416 4.58579 19.6642C4.80836 19.8868 5.13538 20.0542 5.84143 20.1491C6.57625 20.2479 7.55752 20.25 9 20.25H15C16.4425 20.25 17.4238 20.2479 18.1586 20.1491C18.8646 20.0542 19.1916 19.8868 19.4142 19.6642C19.6368 19.4416 19.8042 19.1146 19.8991 18.4086C19.9979 17.6737 20 16.6925 20 15.25V10.75C20 10.1977 20.4477 9.75 21 9.75C21.5523 9.75 22 10.1977 22 10.75V15.3205C22 16.6747 22.0001 17.7913 21.8813 18.6751C21.7565 19.6029 21.4845 20.4223 20.8284 21.0784C20.1723 21.7345 19.3529 22.0065 18.4251 22.1312C17.5413 22.2501 16.4247 22.25 15.0706 22.25H8.92943C7.57531 22.25 6.4587 22.2501 5.57494 22.1312C4.64711 22.0065 3.82768 21.7345 3.17158 21.0784C2.51547 20.4223 2.2435 19.6029 2.11875 18.6751C1.99994 17.7913 1.99997 16.6747 2 15.3206V10.75C2 10.1977 2.44772 9.75 3 9.75Z" fill="currentColor"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M8.25214 16.3362C8.61876 15.9231 9.25081 15.8855 9.66385 16.2521C10.1297 16.6656 10.9662 17 12 17C13.0338 17 13.8704 16.6656 14.3362 16.2521C14.7492 15.8855 15.3813 15.9231 15.7479 16.3362C16.1145 16.7492 16.0769 17.3813 15.6639 17.7479C14.7615 18.5489 13.4197 19 12 19C10.5803 19 9.23856 18.5489 8.33617 17.7479C7.92313 17.3813 7.88551 16.7492 8.25214 16.3362Z" fill="currentColor"/>
<path d="M3.19143 4.45934C3.19143 2.95786 4.41603 1.75 5.91512 1.75H18.0849C19.584 1.75 20.8086 2.95786 20.8086 4.45934C20.8086 5.00972 20.9532 5.55089 21.2287 6.02939L22.2149 7.74274C22.4737 8.19195 22.6839 8.55669 22.7347 9.16669C22.7553 9.41456 22.7576 9.62312 22.726 9.82441C22.6958 10.0172 22.6381 10.1717 22.5956 10.2854L22.5894 10.3023C22.0565 11.7329 20.6723 12.75 19.0513 12.75C17.695 12.75 16.5023 12.037 15.8374 10.9644C14.9338 12.0575 13.5446 12.75 12 12.75C10.4554 12.75 9.06617 12.0575 8.16259 10.9644C7.49773 12.037 6.30506 12.75 4.94875 12.75C3.32768 12.75 1.94355 11.7329 1.41065 10.3022L1.40436 10.2854C1.3619 10.1717 1.30421 10.0172 1.27397 9.82441C1.2424 9.62312 1.24469 9.41457 1.26533 9.16669C1.31613 8.55668 1.52628 8.19195 1.78509 7.74274L2.77133 6.02939C3.04677 5.55089 3.19143 5.00972 3.19143 4.45934Z" fill="currentColor"/>
</svg>

                <span>
                    {{ $branch->name ?? '' }}
                </span>
            </a>
            @endif
            <div class="language-change">
                <div class="dropdown">
                    <button class="btn btn-light rounded-full dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}"
                            alt="" class="flag-icon me-2">
                        {{ languages()[app()->getLocale()]['name'] }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-scroll">
                        @foreach (languages() as $key => $language)
                            <li class="language-li">
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                    <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt=""
                                        class="flag-icon me-2">
                                    {{ $language['name'] }}
                                </a>
                                @if (app()->getLocale() == $key)
                                    <i class="fas fa-check language-check"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="notifications dropdown">
                <a href="#" class="dropdown-toggleer " data-bs-toggle="dropdown">
                    <i><img src="{{ asset('assets/images/icons/bel.svg') }}" alt=""></i>
                    <span class="bg-red">{{ auth()->user()->unreadNotifications->count() }}</span>
                </a>
                <div class="dropdown-menu notification-container">
                    <div class="notification-header ">
                        <a href="{{ route('business.notifications.mtReadAll') }}"
                            class="text-red">{{ __('Mark all Read') }}</a>
                    </div>
                    <ul>
                        @foreach (auth()->user()->unreadNotifications  as $notification)
                            <li>
                                <a href="{{ route('business.notifications.mtView', $notification->id) }}">
                                    <strong>{{ __($notification->data['message'] ?? '') }}</strong>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="notification-footer">
                        <a class="text-red" href="{{ route('business.notifications.index') }}">{{ __('View all notifications') }}</a>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center">
                <div class="profile-info dropdown">
                    <a href="#" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="greet-name">
                                <!-- <p class="nav-greeting">{{__('Hello')}}🖐</p> -->
                                <h6 class="nav-name">
                                    {{ auth()->user()->role == 'staff' ? (optional(auth()->user()->business)->companyName . ' [' . auth()->user()->name . ']')  : optional(auth()->user()->business)->companyName }}
                                </h6>
                            </div>
                            <img src="{{ asset(auth()->user()->image ?? 'assets/images/icons/default-user.png') }}" alt="Profile">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-scroll">
                        <li>
                            <a class="dropdown-item" href="{{ route('business.profiles.index') }}"> 
                                <i class="fal fa-user"></i>
                                {{ __('My Profile') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" class="logoutButton">
                                <i class="far fa-sign-out"></i> {{ __('Logout') }}
                                <form action="{{ route('logout') }}" method="post" id="logoutForm">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-opner menu-openerr"><i class="fal fa-bars" aria-hidden="true"></i></div>
            </div>
        </div>
    </div>
</header>

@push('modal')
    <div class="modal fade custom-modal" id="exitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <span>
                                            <img src="{{ asset('assets/images/dashboard/main2.svg') }}" alt="" style=" color: #FF6500;">

                    </span>
                    <h5 class="exit-title">{{__('Are you sure you want to exit?')}}</h5>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn-no" data-bs-dismiss="modal">{{__('No')}}</button>
                        <a href="javascript:void(0)" class="btn-yes exit-branch">{{__('Yes')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix dropdown functionality on mobile devices
    function initMobileDropdowns() {
        // Language dropdown
        const languageToggle = document.querySelector('.language-change .dropdown-toggle');
        const languageMenu = document.querySelector('.language-change .dropdown-menu');
        
        // Profile dropdown
        const profileToggle = document.querySelector('.profile-info .dropdown-toggle, .profile-info a[data-bs-toggle="dropdown"]');
        const profileMenu = document.querySelector('.profile-info .dropdown-menu');
        
        // Notification dropdown
        const notificationToggle = document.querySelector('.notifications .dropdown-toggleer');
        const notificationMenu = document.querySelector('.notifications .dropdown-menu, .notifications .notification-container');
        
        // Function to close all dropdowns
        function closeAllDropdowns() {
            if (languageMenu) languageMenu.classList.remove('show');
            if (profileMenu) profileMenu.classList.remove('show');
            if (notificationMenu) notificationMenu.classList.remove('show');
            document.querySelectorAll('.dropdown-toggle, a[data-bs-toggle="dropdown"]').forEach(toggle => {
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
        
        // Language dropdown functionality
        if (languageToggle && languageMenu) {
            languageToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = languageMenu.classList.contains('show');
                closeAllDropdowns();
                
                if (!isOpen) {
                    languageMenu.classList.add('show');
                    languageToggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
        
        // Profile dropdown functionality
        if (profileToggle && profileMenu) {
            profileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = profileMenu.classList.contains('show');
                closeAllDropdowns();
                
                if (!isOpen) {
                    profileMenu.classList.add('show');
                    profileToggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
        
        // Notification dropdown functionality
        if (notificationToggle && notificationMenu) {
            notificationToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = notificationMenu.classList.contains('show');
                closeAllDropdowns();
                
                if (!isOpen) {
                    notificationMenu.classList.add('show');
                    notificationToggle.setAttribute('aria-expanded', 'true');
                }
            });
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                closeAllDropdowns();
            }
        });
        
        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll('.dropdown-menu, .notification-container').forEach(menu => {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        // Close dropdown when clicking on dropdown items (except for notifications)
        document.querySelectorAll('.language-change .dropdown-item, .profile-info .dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                // Don't close for logout button as it has its own handler
                if (!item.classList.contains('logoutButton')) {
                    closeAllDropdowns();
                }
            });
        });
    }
    
    // Initialize dropdowns
    initMobileDropdowns();
    
    // Reinitialize on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initMobileDropdowns, 250);
    });
});
</script>
@endpush
