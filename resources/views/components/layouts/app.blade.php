<!--
=========================================================
* Argon Dashboard 2 - v2.0.0
=========================================================
* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim
=========================================================
* The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.
-->



<!DOCTYPE html>
<html lang="en">
<head>
  <x-layouts.header/>
  {{ isset($addonstyle) ? $addonstyle : '' }}
</head>
<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
    <x-layouts.sidebar/>
    <main class="main-content position-relative border-radius-lg ">
      <x-layouts.navbar>
        <x-slot:breadcrumb>
          {{ isset($breadcrumb) ? $breadcrumb : '' }}
        </x-slot>
      </x-layouts.navbar>
      <div class="container-fluid py-4">
        
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show " role="alert">
            <span class="text-white">
              {{ session('success') }}
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="text-white">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        @endif

        {{ isset($slot) ? $slot : '' }}
      </div>
      <x-layouts.footer/>
    </main>
  <x-layouts.theme/>
  <x-layouts.script/>
  {{ isset($addonscript) ? $addonscript : '' }}
</body>
</html>