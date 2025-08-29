<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="./index.html" class="brand-link">
            <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">AdminLTE 4</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.products.index')}}" class="nav-link {{ Request::is('admin/products.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-box"></i>
                        <p>
                            Product
                        </p>
                    </a>
                </li>

                <!-- ADD THIS NEW LINK for Lot Inquiries -->
                <li class="nav-item">
                    <a href="{{ route('admin.lot-inquiries.index') }}" class="nav-link {{ request()->routeIs('admin.lot-inquiries.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-patch-question-fill"></i>
                        <p>Lot Inquiries</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

{{--

<li class="nav-item menu-open">
    <a href="#" class="nav-link active">
        <i class="nav-icon bi bi-speedometer"></i>
        <p>
            Dashboard
            <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="./index.html" class="nav-link active">
                <i class="nav-icon bi bi-circle"></i>
                <p>Dashboard v1</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="./index2.html" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Dashboard v2</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="./index3.html" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Dashboard v3</p>
            </a>
        </li>
    </ul>
</li>

--}}
