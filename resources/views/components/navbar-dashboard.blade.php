<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle is-active" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu ficon"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a></li>
        </ul>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link nav-link-style">
                    <i class="ficon" data-feather="moon"></i>
                </a>
            </li>
            <li class="nav-item dropdown dropdown-notification me-25">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <i class="ficon" data-feather="bell"></i>
                    <span class="badge rounded-pill bg-danger badge-up">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 me-auto">الإشعارات</h4>
                            <div class="badge rounded-pill badge-light-primary">0 جديد</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list">
                        <a class="d-flex" href="#">
                            <div class="list-item d-flex align-items-start">
                                {{-- <div class="me-1">
                                    @if(auth()->user()->avatar == null)
                                        <img src="{{ asset('assets/images/web/profile.png') }}" alt="avatar" width="32" height="32">
                                    @else 
                                        <img src="{{ auth()->user()->avatar }}" alt="avatar" width="32" height="32">
                                    @endif
                                </div> --}}
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading">
                                        لا توجد اى إشعارات حتي الآن
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-menu-footer">
                        <a class="btn btn-primary w-100" href="#">
                            قراءة كل الإشعارات
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{ auth()->user()->name }}</span>
                    </div>
                    <span class="avatar">
                        @if(auth()->user()->avatar == null)
                            <img src="{{ asset('assets/images/web/profile.png') }}" alt="avatar" width="32" height="32">
                        @else 
                            <img src="{{ auth()->user()->avatar }}" alt="avatar" width="32" height="32">
                        @endif
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                
                    <a class="dropdown-item" href="{{ route('setting.show') }}">
                        <i class="me-50" data-feather="settings"></i> الإعدادات
                    </a>

                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                        <i class="me-50" data-feather="power"></i> تسجيل الخروج
                    </a>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>