<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">      
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          @if ($notification_count > 0)
          <span class="badge badge-warning navbar-badge">{{ $notification_count }}</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          @if ($notification_count > 0)
          <span class="dropdown-item dropdown-header font-weight-bold">{{ $notification_count }} Notifikasi</span>          
          @else
          <span class="dropdown-item dropdown-header font-weight-bold">Tidak ada notifikasi</span>
          @endif
          @foreach($notifications as $notif)
          <div class="dropdown-divider"></div>          
          <a href="{{ $notif->URL != "" ? $notif->URL : "#" }}" class="dropdown-item">
          <i class="fas fa-users mr-2"></i><span class="text-wrap">{{ $notif->NOTIFICATION }}</span>
          </a>                        
          @endforeach
          <div class="dropdown-divider"></div>
          <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
        </div>
      </li>
      <li class="nav-item dropdown">        
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-user-circle fa-lg"></i>          
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header font-weight-bold">{{ Auth::user()->name }}</span>
          <div class="dropdown-divider"></div>
          <a href="{{ route('settings.index') }}" class="dropdown-item">
          <i class="fa fa-cog m-r-5 m-l-5"></i>
          Settings
          </a>                  
          <a class="dropdown-item" href="{{ route('logout') }}">
            <i class="fa fa-power-off m-r-5 m-l-5"></i>
            Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->  