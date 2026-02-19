<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand bg-black bg-opacity-25 py-3 px-4 border-bottom border-dark">
    <!--begin::Brand Link-->
    <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center text-decoration-none">
      <!--begin::Brand Image-->
      <div class="brand-image-wrapper me-3">
        <img
          src="{{ asset('admin/dist/assets/img/package.png')}}"
          alt="Inventory System Logo"
          class="brand-image"
          style="width: 36px; height: 36px; filter: brightness(1.2);"
        />
      </div>
      <!--end::Brand Image-->
      <!--begin::Brand Text-->
      <div class="brand-text-wrapper">
        <span class="brand-text fw-bold text-white d-block" style="font-size: 1.1rem; letter-spacing: 0.5px;">INVENTORY</span>
        <!-- <span class="brand-subtext text-secondary d-block" style="font-size: 0.75rem; letter-spacing: 1px;">MANAGEMENT</span> -->
      </div>
      <!--end::Brand Text-->
    </a>
    <!--end::Brand Link-->
  </div>
  <!--end::Sidebar Brand-->
  
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper py-3">
    <nav>
      <!--begin::Sidebar Menu-->
      <ul
        class="nav sidebar-menu flex-column px-2"
        data-lte-toggle="treeview"
        role="menu"
        data-accordion="false">
        
        <!-- Menu Header -->
        <li class="nav-header text-uppercase text-secondary mt-1 mb-2 px-3" style="font-size: 0.7rem; letter-spacing: 1px;">
          Main Navigation
        </li>

        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link rounded-3 px-3 py-2 mb-1">
            <i class="nav-icon bi bi-speedometer2 me-3"></i>
            <p class="mb-0">Dashboard</p>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('admin.deployment') }}" class="nav-link rounded-3 px-3 py-2 mb-1">
            <i class="nav-icon bi bi-truck me-3"></i>
            <p class="mb-0">Deployment</p>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="#" class="nav-link rounded-3 px-3 py-2 mb-1">
            <i class="nav-icon bi bi-boxes me-3"></i>
            <p class="mb-0">
              Inventory
              <i class="nav-arrow bi bi-chevron-right ms-auto"></i>
            </p>
          </a>
          <ul class="nav nav-treeview ps-4 mt-1" style="display: none;">
            <li class="nav-item">
              <a href="{{ route('admin.inventory') }}" class="nav-link rounded-3 px-3 py-2 mb-1">
                <i class="nav-icon bi bi-list-ul me-3"></i>
                <p class="mb-0">Item Inventory</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.category') }}" class="nav-link rounded-3 px-3 py-2">
                <i class="nav-icon bi bi-tags me-3"></i>
                <p class="mb-0">Categories</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.suppliers') }}" class="nav-link rounded-3 px-3 py-2">
                <i class="nav-icon bi bi-people me-3"></i>
                <p class="mb-0">Suppliers</p>
              </a>
            </li>
          </ul>
        </li>
        
        <li class="nav-item">
          <a href="{{ route('admin.reports') }}" class="nav-link rounded-3 px-3 py-2 mb-1">
            <i class="nav-icon bi bi-graph-up me-3"></i>
            <p class="mb-0">Reports</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('admin.contactperson') }}" class="nav-link rounded-3 px-3 py-2">
            <i class="nav-icon bi bi-people me-3"></i>
            <p class="mb-0">Contact Person</p>
          </a>
        </li>
      </ul>
      <!--end::Sidebar Menu-->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
  
  <!--begin::User Panel (Modern Discord-style)-->
  <div class="user-panel mt-auto mx-3 mb-3 p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(255,255,255,0.1);">
    <div class="d-flex align-items-center">
      <!-- User Avatar -->
      <div class="position-relative me-3">
        <div class="avatar-wrapper position-relative">
          <img
            src="{{ asset('admin/dist/assets/img/user2-160x160.jpg') }}"
            class="rounded-circle shadow-sm"
            alt="User Image"
            style="width: 44px; height: 44px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);"
          />
          <!-- Online Status Indicator -->
          <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-dark" 
               style="width: 12px; height: 12px;"></div>
        </div>
      </div>
      
      <!-- User Info -->
      <div class="flex-grow-1">
        <div class="text-white fw-semibold" style="font-size: 0.9rem;">
          @auth
            {{ Auth::user()->name }}
          @else
            Guest User
          @endauth
        </div>
        <div class="text-info" style="font-size: 0.75rem; opacity: 0.8;">
          <i class="bi bi-shield-check me-1" style="font-size: 0.7rem;"></i>
          @auth
            {{ ucfirst(Auth::user()->role ?? 'Administrator') }}
          @else
            Visitor
          @endauth
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="ms-2 d-flex">
        <!-- Settings Button -->
        <button class="btn btn-sm btn-outline-secondary border-0 p-1 me-1" title="Settings" style="width: 32px; height: 32px;">
          <i class="bi bi-gear"></i>
        </button>
        
        <!-- Logout Button -->
        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Logout" style="width: 32px; height: 32px;">
            <i class="bi bi-box-arrow-right"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
  <!--end::User Panel-->
</aside>

<style>
  /* Custom Sidebar Styles */
  .app-sidebar {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    color: #e9ecef;
    width: 260px;
    transition: all 0.3s ease;
  }

  /* Brand Styling */
  .sidebar-brand {
    backdrop-filter: blur(10px);
  }
  
  .brand-image {
    transition: transform 0.3s ease;
  }
  
  .brand-link:hover .brand-image {
    transform: rotate(-5deg) scale(1.1);
  }

  /* Menu Item Styling */
  .sidebar-menu .nav-link {
    color: #adb5bd;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    margin: 2px 0;
    display: flex;
    align-items: center;
  }
  
  .sidebar-menu .nav-link:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.1);
    padding-left: 20px !important;
  }
  
  .sidebar-menu .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }
  
  .sidebar-menu .nav-link.active i {
    color: white !important;
  }

  /* Submenu Styling */
  .nav-treeview .nav-link {
    background: rgba(0, 0, 0, 0.2);
    font-size: 0.9rem;
    padding: 0.5rem 1rem !important;
    margin: 1px 0;
  }
  
  .nav-treeview .nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
    padding-left: 1.5rem !important;
  }

  /* Icons */
  .nav-icon {
    font-size: 1.1rem;
    min-width: 24px;
    color: #6c757d;
  }
  
  .nav-link.active .nav-icon {
    color: white !important;
  }

  /* Badges */
  .badge {
    font-weight: 500;
    padding: 0.2rem 0.5rem;
    font-size: 0.65rem;
  }

  /* User Panel */
  .user-panel {
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
  }
  
  .user-panel:hover {
    background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }
  
  .avatar-wrapper {
    transition: transform 0.3s ease;
  }
  
  .user-panel:hover .avatar-wrapper {
    transform: scale(1.05);
  }

  /* Action Buttons */
  .user-panel .btn-outline-secondary {
    color: #adb5bd;
    border-color: rgba(255, 255, 255, 0.1);
  }
  
  .user-panel .btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
  }
  
  .user-panel .btn-outline-danger:hover {
    background: rgba(220, 53, 69, 0.2);
    color: #ff6b6b;
  }

  /* Arrow Rotation */
  .nav-item.menu-open .nav-arrow {
    transform: rotate(90deg);
  }
  
  .nav-arrow {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
  }

  /* Remove active class and menu-open from inventory */
  .nav-item.menu-open {
    /* Remove any auto-open styling */
  }

  /* Scrollbar Styling */
  .sidebar-wrapper {
    overflow-y: auto;
    max-height: calc(100vh - 180px);
  }
  
  .sidebar-wrapper::-webkit-scrollbar {
    width: 5px;
  }
  
  .sidebar-wrapper::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
  }
  
  .sidebar-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
  }
  
  .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .app-sidebar {
      width: 240px;
    }
  }
</style>