<!--
=========================================================
* Argon Dashboard 2 - v2.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Sistem pencatatan Donasi | LOGIN
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('argon/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('argon/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('argon/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('argon/assets/css/argon-dashboard.css?v=2.0.0') }}" rel="stylesheet" />
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
          <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.html">
              Sistem  Pencatatan Lazis
            </a>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">

              @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <span class="text-white">{{ session('error') }}</span>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              @endif

              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Sign In</h4>
                  <p class="mb-0">Masukkan No telepon dan Passwordnya</p>
                </div>

                <div class="card-body">
                  <form role="form" method="POST" action="{{ route('auth.store') }}">
                    @csrf
                    <div class="mb-3">
                      {{-- <input type="text" name="phone_number" type="text" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" placeholder="phonenumber" aria-label="Phone Number" value="{{ old('phone_number', '+380991234567') }}"> --}}
                      <input type="text" name="phone_number" type="text" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" placeholder="phonenumber" aria-label="Phone Number" value="{{ old('phone_number') }}">
                      @error('phone_number')
                        <span class="invalid-feedback">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="mb-3">
                      {{-- <input type="password" name="password" class="form-control form-control-lg  @error('password') is-invalid @enderror" placeholder="Password" aria-label="Password" value="{{ old('password', 'secret') }}"> --}}
                      <input type="password" name="password" class="form-control form-control-lg  @error('password') is-invalid @enderror" placeholder="Password" aria-label="Password" value="{{ old('password') }}">
                      @error('password')
                        <span class="invalid-feedback">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" name="remember" type="checkbox" id="rememberMe">
                      <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Sign in</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('https://images.unsplash.com/photo-1616386261012-8a328c89d5b6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1077&q=80');
          background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Attention is the new currency"</h4>
                <p class="text-white position-relative">The more effortless the writing looks, the more effort the writer actually put into the process.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  <script src="{{ asset(' argon/assets/js/core/popper.min.js"') }}></script>
  <script src="{{ asset(' argon/assets/js/core/bootstrap.min.js"') }}></script>
  <script src="{{ asset(' argon/assets/js/plugins/perfect-scrollbar.min.js"') }}></script>
  <script src="{{ asset(' argon/assets/js/plugins/smooth-scrollbar.min.js"') }}></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('argon/assets/js/argon-dashboard.min.js?v=2.0.0') }}"></script>
</body>

</html>