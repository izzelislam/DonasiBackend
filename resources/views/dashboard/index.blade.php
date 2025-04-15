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
      .map-controls {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1000;
        background: white;
        padding: 8px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      }
      .map-controls button {
        margin: 2px;
        padding: 5px 10px;
        border: none;
        background: #eee;
        cursor: pointer;
        border-radius: 4px;
      }
      .map-controls button.active {
        background: #007bff;
        color: white;
      }
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
            <div class="map-controls">
              <button id="streetBtn" class="active">Street View</button>
              <button id="satelliteBtn">Satelit View</button>
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
     
    {{-- <script>

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

    </script> --}}


    <script>
      const donors = @json($donors);
    
      const map = L.map('mapid').setView([-7.710991655433217, 110.43434095752946], 8);
    
      const streetLayer = L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=D6GXg3djzKJUSwb88XNc', {
        attribution: '&copy; <a href="https://www.maptiler.com/">MapTiler</a>'
      });
    
      const satelliteLayer = L.tileLayer('https://api.maptiler.com/maps/hybrid/{z}/{x}/{y}.jpg?key=D6GXg3djzKJUSwb88XNc', {
        attribution: '&copy; <a href="https://www.maptiler.com/">MapTiler</a>'
      });
    
      streetLayer.addTo(map); // default view
    
      // Markers
      donors.forEach(donor => {
        L.marker([donor.lat, donor.lng]).addTo(map).bindPopup(`
          <b>${donor.name}</b><br>
          ${donor.phone_number}<br>
          ${donor.address}
        `);
      });
    
      // Button switching logic
      document.getElementById('streetBtn').addEventListener('click', function () {
        map.removeLayer(satelliteLayer);
        map.addLayer(streetLayer);
        this.classList.add('active');
        document.getElementById('satelliteBtn').classList.remove('active');
      });
    
      document.getElementById('satelliteBtn').addEventListener('click', function () {
        map.removeLayer(streetLayer);
        map.addLayer(satelliteLayer);
        this.classList.add('active');
        document.getElementById('streetBtn').classList.remove('active');
      });
    
      // Optional fullscreen
      var elem = document.getElementById("mapid");
      function fullScreeen(){
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
          elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
          elem.msRequestFullscreen();
        }
      }
    </script>
  </x-slot>

</x-layouts.app>