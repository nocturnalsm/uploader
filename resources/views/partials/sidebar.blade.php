<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-dark elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('') }}" class="brand-link">
      <span class="brand-text font-weight-light">{{ config('app.name', "Uploader") }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">      
      @if(count($companies_list) > 0)
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div title="Ganti Perusahaan" class="image">
          <i class="fa fa-arrow-circle-right fa-2x text-white fa-fw"></i>
        </div>
        <div class="info">
          <a href="#" class="d-block font-weight-bold text-wrap">{{ $current_company->NAME }}</a>
          @if(count($companies_list) > 1)
          <div class="dropdown">
            <a class="dropdown-toggle" href="#" data-toggle="dropdown">
              Ganti Perusahaan
            </a>
            <form id="company-change" action="{{ route('settings.current_company') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="_current_company" name="_current_company" value="">
            </form>
            <div class="dropdown-menu">
              @foreach($companies_list as $comp)
              <a onclick="document.getElementById('_current_company').value='{{ $comp->COMPANY_ID }}';document.getElementById('company-change').submit()" class="dropdown-item" data-id="{{ $comp->COMPANY_ID }}" href="#">{{ $comp->NAME }}</a>
              @endforeach
            </div>
          </div>          
          @endif
        </div>        
      </div>
      @endif
      

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->    
          @can('document.list')
          <li class="nav-item">
            <a href="{{ route('document.index') }}" class="nav-link">
              <i class="nav-icon fa fa-book-open"></i>
              <p>
                Daftar Dokumen                
              </p>
            </a>
          </li>              
          @endcan
          @can('company.list')
          <li class="nav-item">
            <a href="{{ route('company.index') }}" class="nav-link">
              <i class="nav-icon fa fa-building"></i>
              <p>Perusahaan</p>
            </a>
          </li>
          @endcan
          @if (auth()->user()->can("user.list") || auth()->user()->can("role.list"))
          <li class="nav-item has-treeview{{ Request::is('admin/*') ? ' menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Admin
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('user.list')
              <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>User</p>
                </a>
              </li>
              @endcan
              @can('role.list')
              <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Group User</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>          
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>