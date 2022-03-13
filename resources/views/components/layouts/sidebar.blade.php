@php
    $setting = \App\Models\Setting::first();
@endphp
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-5 fixed-start ms-4 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    {{-- <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
    </a> --}}
    <div class="px-5 pt-2">
      <img src="{{ Storage::url($setting->image ?? '') }}"  style="width: 85px;" alt="main_logo">
    </div>
  </div>
  
  <div class="collapse navbar-collapse  w-auto h-auto h-100" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <li class="nav-item mt-3">
        <hr class="horizontal dark mt-0">
        <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder opacity-6">MENU UTAMA</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link
        @if(Request::is('dashboard*'))
            active
        @endif
        " href="{{ route('dashboard') }}">
          <div class="icon icon-shape icon-sm text-center  me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-spaceship text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      
      {{-- donatur --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#donors" class="nav-link 
        @if(Request::is('donors*'))
          active
        @endif
        " aria-controls="donors" role="button" aria-expanded="{{ Request::is('donors*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Donatur</span>
        </a>
        <div class="collapse 
        @if(Request::is('donors*'))
          show
        @endif
        " id="donors">
          <ul class="nav ms-4">
            <li class="nav-item">
              <a class="nav-link " href="{{ route('donors.index') }}">
                <span class="sidenav-normal"> daftar donatur </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('donors.create') }}">
                <span class="sidenav-normal"> buat donatur </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      {{-- donasi --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#donation" class="nav-link 
        @if(Request::is('donations*'))
          active
        @endif
        " aria-controls="donation" role="button" aria-expanded="{{ Request::is('donations*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-archive-2 text-success text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Donasi</span>
        </a>
        <div class="collapse 
        @if(Request::is('donations*'))
          show
        @endif
        " id="donation">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('donations.index') }}">
                <span class="sidenav-normal"> list donasi </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('donations.create') }}">
                <span class="sidenav-normal"> buat donasi </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      {{-- statistik --}}
      <li class="nav-item">
        <a class="nav-link 
        @if(Request::is('statistic*'))
          active
       @endif" 
       href="/statistic" >
          <div class="icon icon-shape icon-sm text-center  me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-chart-bar-32 text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Statistik</span>
        </a>
      </li>

      {{-- keuangan --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#finance" class="nav-link 
        @if(Request::is('financials*'))
          active
        @endif
        " aria-controls="finance" role="button" aria-expanded="{{ Request::is('financials*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-money-coins text-success text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Keuangan</span>
        </a>
        <div class="collapse 
        @if(Request::is('financials*'))
          show
        @endif
        " id="finance">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('financials.index') }}">
                <span class="sidenav-normal"> list keuangan </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('financials.create') }}">
                <span class="sidenav-normal"> tambah catatan </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      {{-- laporan --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#ecommerceExamples" class="nav-link 
        @if(Request::is('reports*'))
          active
        @endif
        " aria-controls="ecommerceExamples" role="button" aria-expanded="{{ Request::is('reports*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Laporan</span>
        </a>
        <div class="collapse 
        @if(Request::is('reports*'))
          show
        @endif
        " id="ecommerceExamples">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('reports.donations') }}">
                <span class="sidenav-normal"> laporan donasi </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('reports.financials') }}">
                <span class="sidenav-normal"> laporan keuangan </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item mt-3">
        <hr class="horizontal dark" />
        <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder opacity-6">MASTER DATA</h6>
      </li>

      {{-- tim --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link 
        @if(Request::is('teams*'))
          active
        @endif
        " aria-controls="pagesExamples" role="button" aria-expanded="{{ Request::is('teams*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-ungroup text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tim</span>
        </a>
        <div class="collapse 
        @if(Request::is('teams*'))
          show
        @endif
        " id="pagesExamples">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('teams.index') }}">
                <span class="sidenav-normal"> list Tim </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('teams.create') }}">
                <span class="sidenav-normal"> tambah Tim </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      {{-- fundriser --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#fundriser" class="nav-link 
        @if(Request::is('fundrisers*'))
          active
        @endif
        " aria-controls="fundriser" role="button" aria-expanded="{{ Request::is('fundrisers*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-delivery-fast text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Fundriser</span>
        </a>
        <div class="collapse 
        @if(Request::is('fundrisers*'))
          show
        @endif
        " id="fundriser">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('fundrisers.index') }}">
                <span class="sidenav-normal"> list fundriser </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('fundrisers.create') }}">
                <span class="sidenav-normal"> tambah fundriser </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      
      {{-- other --}}
      <li class="nav-item">
        <hr class="horizontal dark" />
        <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder opacity-6">SISTEM</h6>
      </li>

      {{-- manajemen user --}}
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#componentsExamples" class="nav-link 
        @if(Request::is('users*'))
          active
        @endif
        " aria-controls="componentsExamples" role="button" aria-expanded="{{ Request::is('users*') ? 'true' : 'false' }}">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-user-run text-dark text-sm"></i>
          </div>
          <span class="nav-link-text ms-1">User Manajemen</span>
        </a>
        <div class="collapse 
        @if(Request::is('users*'))
          show
        @endif
        " id="componentsExamples">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link " href="{{ route('users.index') }}">
                <span class="sidenav-normal"> list user </span>
              </a>
            </li>

            <li class="nav-item ">
              <a class="nav-link " href="{{ route('users.create') }}">
                <span class="sidenav-normal"> buat user </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      {{-- setting --}}
      <li class="nav-item">
        <a class="nav-link
        @if(Request::is('settings*'))
            active
        @endif
        " href="{{ route('settings.index') }}">
          <div class="icon icon-shape icon-sm text-center  me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-settings text-danger text-sm"></i>
          </div>
          <span class="nav-link-text ms-1">Setting</span>
        </a>
      </li>

    </ul>
  </div>
  <div class="sidenav-footer mx-3 my-3">
    <div class="card card-plain shadow-none" id="sidenavCard">
      <div class="card-body text-center p-3 w-100 pt-0">
        <form action="{{ route('auth.logout') }}" method="POST">
          @csrf
          <div class="docs-info">
            <button target="_blank" class="btn btn-primary btn-sm w-100 mb-0">Logout</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</aside>