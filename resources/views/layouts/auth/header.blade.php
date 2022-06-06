<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ _vers('images/logo/coop_hp.png') }}" alt="Coop Service" height="60" width="60">
    <span class="brand-text font-weight-bold text-blue">コープやまぐち</span>
</div>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{ \Auth::user()->name }}
                <i class="fas fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ \Auth::user()->email }}</span>
                <div class="dropdown-divider"></div>
                {{-- TODO: profile edit --}}
                {{-- <a href="#" class="dropdown-item">
                    <i class="fas fa-id-card mr-2"></i> {{ __('words.Profile') }}
                </a> --}}
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer"><i class="fas fa-power-off text-red"></i> <strong>{{ __('words.Logout') }}</strong></a>
            </div>
        </li>
    </ul>
</nav>