<div class="sidebar">
    <div class="text-center mb-4">
        <h4 class="text-white">Admin Panel</h4>
    </div>
    
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Dashboard
    </a>
    
    <a href="{{ route('admin.listings.index') }}" class="{{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">
        <i class="fas fa-list"></i> Listings
    </a>
    
    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Users
    </a>
    
    <a href="{{ route('admin.dealers.index') }}" class="{{ request()->routeIs('admin.dealers.*') ? 'active' : '' }}">
        <i class="fas fa-store"></i> Dealers
    </a>
    
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="fas fa-flag"></i> Reports
    </a>
    
    <a href="{{ route('admin.reports.analytics') }}" class="{{ request()->routeIs('admin.reports.analytics') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Analytics
    </a>
    
    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i> Categories
    </a>
    
    <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt"></i> Locations
    </a>
    
    <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i> Content Management
    </a>
    
    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <i class="fas fa-user-shield"></i> Admin Roles
    </a>
    
    <a href="{{ route("admin.banners.index") }}" class="{{ request()->routeIs("admin.banners.*") ? "active" : "" }}">
        <i class="fas fa-image"></i> Banners
    </a>
        <a href="{{ route('admin.advertisements.index') }}" class="{{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
        <i class="fas fa-bullhorn"></i> Advertisements
    </a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
        <i class="fas fa-star"></i> Reviews
    </a>
        <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i> Notifications
    </a>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i> Settings
    </a>
        <a href="{{ route('admin.custom-fields.index') }}" class="{{ request()->routeIs('admin.custom-fields.*') ? 'active' : '' }}">
        <i class="fas fa-th-list"></i> Custom Fields
    </a>
</div>
