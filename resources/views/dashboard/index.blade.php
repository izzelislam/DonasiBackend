@php
    $donors = DB::table('donors')->get();
@endphp
<x-layouts.app>

  <x-slot:addonstyle>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.css' rel='stylesheet'>

    <style>
      #mapid { min-height: 500px; }
    </style>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="dashboard"
      url="dashboard"
      title="Dashboard"
    />
  </x-slot>

  <div class="container-fluid py-4">
    @include('dashboard.card-item')
 
    {{-- map --}}
    <div class="row mt-4">
      <div class="col-lg-12 mb-lg-0 mb-4">
        <div class="card ">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between">
              <h6 class="mb-2">Peta Lokasi Donatur</h6>

              <button onclick="fullScreeen()" class="btn">
                <i class="fa fa-expand"></i>
                Full screen
              </button>
            </div>
          </div>
          <div class="card-body">
            <div id="mapid">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- @include('dashboard.donor-regency') --}}
  </div>
  

  <x-slot:addonscript>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>
    
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
     
    <script>

      const donors = @json($donors);

      const map = L.map('mapid').setView([-7.710991655433217, 110.43434095752946], 8);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      var markered = '';

      
      donors.forEach(donor => {
         L.marker([donor.lat, donor.lng]).addTo(map).bindTooltip(`
            <b>${donor.name}</b><br>
            ${donor.phone_number}<br>
            ${donor.address}<br>
         `);
       });

      console.log(donors);

      var elem = document.getElementById("mapid");

      function fullScreeen(){
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) { /* Safari */
          elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
          elem.msRequestFullscreen();
        }
      }

    </script>
  </x-slot>

</x-layouts.app>