<nav class="app-header navbar navbar-expand bg-body">
  <!--begin::Container-->
  <div class="container-fluid">
    <!--begin::Start Navbar Links-->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
      <!-- <li class="nav-item d-none d-md-block">
        <span class="nav-link">
          <i class="bi bi-house me-1"></i>
          Admin Dashboard
        </span>
      </li> -->
    </ul>
    <!--end::Start Navbar Links-->
    
    <!--begin::End Navbar Links-->
    <ul class="navbar-nav ms-auto">

      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
        </a>
      </li>
      
      <!--begin::Fullscreen Toggle-->
      <li class="nav-item">
        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
          <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
          <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
        </a>
      </li>
      <!--end::Fullscreen Toggle-->
      
      <!-- Keep only essential navbar items -->
    </ul>
    <!--end::End Navbar Links-->
  </div>
  <!--end::Container-->
</nav>

<style>
  /* Custom badge styling */
  .navbar-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 0.6rem;
    padding: 0.15rem 0.35rem;
  }
</style>