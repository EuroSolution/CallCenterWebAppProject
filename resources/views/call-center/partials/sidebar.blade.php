<div class="sidebar">
    <!-- SidebarSearch Form -->
    <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{route('callCenter.dashboard')}}" class="nav-link {{request()->is('dashboard') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('callCenter.categories')}}" class="nav-link {{request()->is('categories') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Categories</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('callCenter.products')}}" class="nav-link {{request()->is('products') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Products</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('callCenter.orders')}}" class="nav-link {{request()->is('orders') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Orders</p>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a href="{{route('callCenter.restaurants')}}"--}}
{{--                   class="nav-link {{ str_contains(url()->current(), 'restaurants') ? 'active' : '' }}">--}}
{{--                    <i class="nav-icon fas fa-utensils"></i>--}}
{{--                    <p>Restaurants</p>--}}
{{--                </a>--}}
{{--            </li>--}}

            <li class="nav-item">
                <a href="{{route('logout')}}"
                   class="nav-link">
                    <i class="nav-icon fa fa-lock"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
