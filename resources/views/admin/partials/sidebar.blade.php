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
                <a href="{{url('dashboard')}}" class="nav-link {{request()->is('dashboard') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard
{{--                        <i class="right fas fa-angle-left"></i>--}}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.setting')}}" class="nav-link {{request()->is('setting') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Site Setting</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.restaurants')}}"
                   class="nav-link {{ str_contains(url()->current(), 'restaurants') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-utensils"></i>
                    <p>Restaurants</p>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a href="{{route('admin.customers')}}"--}}
{{--                   class="nav-link {{ str_contains(url()->current(), 'customers') ? 'active' : '' }}">--}}
{{--                    <i class="nav-icon fa fa-user"></i>--}}
{{--                    <p>Customers</p>--}}
{{--                </a>--}}
{{--            </li>--}}
            <li class="nav-item">
                <a href="{{route('admin.staffMember')}}"
                   class="nav-link {{ str_contains(url()->current(), 'staffMember') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-user"></i>
                    <p>Call Center Users</p>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a href="{{route('admin.orders')}}"--}}
{{--                   class="nav-link {{ str_contains(url()->current(), 'order') ? 'active' : '' }}">--}}
{{--                    <i class="nav-icon fa fa-boxes"></i>--}}
{{--                    <p>Orders</p>--}}
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
