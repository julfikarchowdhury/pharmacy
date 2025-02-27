<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset(setting()->logo) }}" alt="" style="height:60px;max-width:80px">
        </div>
        <div class="sidebar-brand-text mx-3">{{ setting()->app_name }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item @if(request()->is('dashboard')) active @endif">
        <a class="nav-link" href="{{route('admin.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Orders -->
    <li class="nav-item @if(request()->is('orders*')) active @endif">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Orders</span>
        </a>
    </li>

    <!-- Nav Item - Users -->
    <li class="nav-item @if(request()->is('users*')) active @endif">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>All Users</span>
        </a>
    </li>

    <!-- Nav Item - Pharmacies -->
    <li class="nav-item @if(request()->is('pharmacies*')) active @endif">
        <a class="nav-link" href="{{ route('pharmacies.index') }}">
            <i class="fas fa-fw fa-clinic-medical"></i>
            <span>All Pharmacies</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Categories -->
    <li class="nav-item @if(request()->is('categories*')) active @endif">
        <a class="nav-link" href="{{ route('categories.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Categories</span>
        </a>
    </li>

    <!-- Nav Item - Medicine Companies -->
    <li class="nav-item @if(request()->is('medicine_companies*')) active @endif">
        <a class="nav-link" href="{{ route('medicine_companies.index') }}">
            <i class="fas fa-fw fa-industry"></i>
            <span>Medicine Companies</span>
        </a>
    </li>

    <!-- Nav Item - Medicine Generics -->
    <li class="nav-item @if(request()->is('medicine_generics*')) active @endif">
        <a class="nav-link" href="{{ route('medicine_generics.index') }}">
            <i class="fas fa-fw fa-capsules"></i>
            <span>Medicine Generics</span>
        </a>
    </li>

    <!-- Nav Item - Concentrations -->
    <li class="nav-item @if(request()->is('concentrations*')) active @endif">
        <a class="nav-link" href="{{ route('concentrations.index') }}">
            <i class="fas fa-fw fa-flask"></i>
            <span>Concentrations</span>
        </a>
    </li>

    <!-- Nav Item - Sliders -->
    <li class="nav-item @if(request()->is('sliders*')) active @endif">
        <a class="nav-link" href="{{ route('sliders.index') }}">
            <i class="fas fa-fw fa-images"></i>
            <span>Sliders</span>
        </a>
    </li>

    <!-- Medicines Section -->
    <li class="nav-item @if(request()->is('medicines*')) active @endif">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMedicines"
            aria-expanded="true" aria-controls="collapseMedicines">
            <i class="fas fa-fw fa-pills"></i>
            <span>Medicines</span>
        </a>
        <div id="collapseMedicines"
            class="collapse @if(request()->is('medicines*') || request()->is('requested-medicines*')) show @endif"
            aria-labelledby="headingMedicines" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item @if(request()->is('requested-medicines*')) active @endif"
                    href="{{ route('requested_medicines') }}">Medicine Requests</a>
                <a class="collapse-item @if(request()->is('medicines*')) active @endif"
                    href="{{ route('medicines.index') }}">All Medicines</a>
                <a class="collapse-item @if(request()->is('medicines/create')) active @endif"
                    href="{{ route('medicines.create') }}">Add Medicine</a>
            </div>
        </div>
    </li>

    <!-- Tips Section -->
    <li class="nav-item @if(request()->is('tips*')) active @endif">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTips" aria-expanded="true"
            aria-controls="collapseTips">
            <i class="fas fa-fw fa-lightbulb"></i>
            <span>Tips</span>
        </a>
        <div id="collapseTips" class="collapse @if(request()->is('tips*')) show @endif" aria-labelledby="headingTips"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item @if(request()->is('tips*')) active @endif" href="{{ route('tips.index') }}">All
                    Tips</a>
                <a class="collapse-item @if(request()->is('tips/create')) active @endif"
                    href="{{ route('tips.create') }}">Add Tip</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Settings Section -->
    <li class="nav-item @if(request()->is('settings')) active @endif">
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



{{-- units should never be visible to client as unit will only be pc and strip. else there will be issue with
handaling the price --}}
<li class="nav-item active d-none">
    <a class="nav-link" href="{{ route('units.index') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Units</span></a>
</li>