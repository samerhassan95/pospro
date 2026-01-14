<header class="main-header-section sticky-top d-print-none">
    <div class="d-flex align-items-center justify-content-between">
        <div class="bg-white d-flex align-items-center">
            <div class="sidebar-opner menu-opener"><i class="fal fa-bars" aria-hidden="true"></i></div>
            <a class="pos-logo" href="{{ route('admin.dashboard.index') }}"><img src="{{ asset(get_option('general')['common_header_logo'] ?? 'assets/images/logo/backend_logo.png') }}" alt="Logo"></a>
        </div>

        <div class="header-right d-flex align-items-center">
            <a target="_blank" class="text-custom-primary view-website" href="{{ route('home') }}">
<svg width="28" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-32">
  <path d="M21.721 12.752a9.711 9.711 0 0 0-.945-5.003 12.754 12.754 0 0 1-4.339 2.708 18.991 18.991 0 0 1-.214 4.772 17.165 17.165 0 0 0 5.498-2.477ZM14.634 15.55a17.324 17.324 0 0 0 .332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 0 0 .332 4.647 17.385 17.385 0 0 0 5.268 0ZM9.772 17.119a18.963 18.963 0 0 0 4.456 0A17.182 17.182 0 0 1 12 21.724a17.18 17.18 0 0 1-2.228-4.605ZM7.777 15.23a18.87 18.87 0 0 1-.214-4.774 12.753 12.753 0 0 1-4.34-2.708 9.711 9.711 0 0 0-.944 5.004 17.165 17.165 0 0 0 5.498 2.477ZM21.356 14.752a9.765 9.765 0 0 1-7.478 6.817 18.64 18.64 0 0 0 1.988-4.718 18.627 18.627 0 0 0 5.49-2.098ZM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 0 0 1.988 4.718 9.765 9.765 0 0 1-7.478-6.816ZM13.878 2.43a9.755 9.755 0 0 1 6.116 3.986 11.267 11.267 0 0 1-3.746 2.504 18.63 18.63 0 0 0-2.37-6.49ZM12 2.276a17.152 17.152 0 0 1 2.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0 1 12 2.276ZM10.122 2.43a18.629 18.629 0 0 0-2.37 6.49 11.266 11.266 0 0 1-3.746-2.504 9.754 9.754 0 0 1 6.116-3.985Z" />
</svg>


                <!-- {{ __('View Website') }} -->
            </a>


            <div class="language-change">
                <div class="dropdown">
                    <button class="btn btn-light rounded-full dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}" alt="" class="flag-icon me-2">
                        {{ languages()[app()->getLocale()]['name'] }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-scroll">
                        @foreach (languages() as $key => $language)
                            <li class="language-li">
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                    <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt="" class="flag-icon me-2">
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

            @if (auth()->user()->role == 'superadmin')
            <div class="notifications dropdown">
                <a href="#" class="dropdown-toggleer mt-1 me-2" data-bs-toggle="dropdown">
                    <i><img src="{{ asset('assets/images/icons/bel.svg') }}" alt=""></i>
                    <span class="bg-red">{{ auth()->user()->unreadNotifications->count() }}</span>
                </a>
                <div class="dropdown-menu notification-container">
                    <div class="notification-header">
                        <p>{{ __('You Have') }} <strong>{{ auth()->user()->unreadNotifications->count() }}</strong> {{ __('new Notifications') }}</p>
                        <a href="{{ route('admin.notifications.mtReadAll') }}" class="text-red">{{ __('Mark all Read') }}</a>
                    </div>
                    <ul>
                        @foreach (auth()->user()->unreadNotifications as $notification)
                        <li>
                            <a href="{{ route('admin.notifications.mtView', $notification->id) }}">
                                <strong>{{ __($notification->data['message'] ?? '') }}</strong>
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="notification-footer">
                        <a class="text-red" href="{{ route('admin.notifications.index') }}">{{ __('View all notifications') }}</a>
                    </div>
                </div>
            </div>
            @endif

            <div class="d-flex align-items-center justify-content-center">
                <div class="profile-info dropdown">
                    <a href="#" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="greet-name">
                                <h6 class="nav-name">
                                    {{ auth()->user()->name }}
                                    @if(auth()->user()->role == 'superadmin')
                                        <small class="text-muted">({{ __('Super Admin') }})</small>
                                    @endif
                                </h6>
                            </div>
                            <img src="{{ asset(Auth::user()->image ?? 'assets/images/icons/default-user.png') }}" alt="Profile">
                        </div>
                    </a>
                    <div class="business-profile bg-success">
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('cache-clear') }}"><i class="far fa-undo"></i> {{ __('Clear cache') }}</a></li>
                            <li><a href="{{ route('admin.profiles.index') }}"><i class="fal fa-user"></i> {{ __('My Profile') }}</a></li>
                            <li>
                                <a href="javascript:void(0)" class="logoutButton">
                                    <i class="far fa-sign-out"></i> {{ __('Logout') }}
                                    <form action="{{ route('logout') }}" method="post" id="logoutForm">
                                        @csrf
                                    </form>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
