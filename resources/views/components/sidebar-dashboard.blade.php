<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a href="{{ route('home') }}">
                    <x-logo-component />
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
                    <i data-feather="home"></i>
                    <span class="menu-title text-truncate">الرئيسية</span>
                </a>
            </li>

            <li class="nav-item has-sub">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='file-text'></i>
                    <span class="menu-title text-truncate">إدارة الفواتير</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('supplier.invoice.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">فواتير الموردين</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-sub">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='activity'></i>
                    <span class="menu-title text-truncate">إدارة الحسابات</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('account.add') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">إضافة حساب جديد</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="d-flex align-items-center" href="{{ route('account.index') }}">
                            <i data-feather='credit-card'></i>
                            <span class="menu-title text-truncate">الحسابات المالية</span>
                        </a>
                    </li>
                </ul>
            </li>

            
            <li class="nav-item has-sub">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='pocket'></i>
                    <span class="menu-title text-truncate">المنتجات</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('units.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">وحدات القياس</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('size.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">المقاسات</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('category.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">التصنيفات</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('product.add') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">إضافة منتج جديد</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('product.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">كل المنتجات</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="#">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">عرض الأسعار</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-sub">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='user'></i>
                    <span class="menu-title text-truncate">الموردين</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('supplier.add') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">إضافة مورد جديد</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('supplier.index') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">الموردين</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="d-flex align-items-center" href="{{ route('warehouse.index') }}">
                    <i data-feather='credit-card'></i>
                    <span class="menu-title text-truncate">الخزن</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="d-flex align-items-center" href="{{ route('storesHouse.index') }}">
                    <i data-feather='database'></i>
                    <span class="menu-title text-truncate">المخزن</span>
                </a>
            </li>

                        
            <li class="nav-item has-sub">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather='settings'></i>
                    <span class="menu-title text-truncate">الضبط العام</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="d-flex align-items-center" href="{{ route('setting.show') }}">
                            <i data-feather='circle'></i>
                            <span class="menu-item text-truncate">إعدادات الشركة</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="d-flex align-items-center" href="{{ route('backup.view') }}">
                    <i data-feather='database'></i>
                    <span class="menu-title text-truncate">النسخ الإحتياطي</span>
                </a>
            </li>

        </ul>
    </div>
</div>