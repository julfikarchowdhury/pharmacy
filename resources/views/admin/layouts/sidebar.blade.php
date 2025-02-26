<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon">
            <img src="{{ asset(setting()->logo) }}" alt="" style="height:60px;max-width:80px">
        </div>
        <div class="sidebar-brand-text mx-3">{{ setting()->app_name }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{route('admin.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Orders</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>All User</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('pharmacies.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>All Pharmacy</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('categories.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Categories</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('medicine_companies.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Medicine Company</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('medicine_generics.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Medicine Generics</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('concentrations.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Concentrations</span></a>
    </li>
    {{-- units should never be visible to client as unit will only be pc and strip. else there will be issue with
    handaling the price --}}
    <li class="nav-item active d-none">
        <a class="nav-link" href="{{ route('units.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Units</span></a>
    </li>
    <!-- Enrollments Section -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEnrollments"
            aria-expanded="true" aria-controls="collapseEnrollments">
            <i class="fas fa-fw fa-box"></i>
            <span>Medicines</span>
        </a>
        <div id="collapseEnrollments" class="collapse" aria-labelledby="headingEnrollments"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('requested_medicines') }}">Medicine Requests</a>
                <a class="collapse-item" href="{{ route('medicines.index') }}">All Medicine</a>
                <a class="collapse-item" href="{{ route('medicines.create') }}">Add Medicine</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEnrollments"
            aria-expanded="true" aria-controls="collapseEnrollments">
            <i class="fas fa-fw fa-box"></i>
            <span>Tips</span>
        </a>
        <div id="collapseEnrollments" class="collapse" aria-labelledby="headingEnrollments"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('tips.index') }}">All Tips</a>
                <a class="collapse-item" href="{{ route('tips.create') }}">Add Tip</a>
            </div>
        </div>
    </li>

    <!-- Settings Section -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('settings')}}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Settings</span>
        </a>
    </li>

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>