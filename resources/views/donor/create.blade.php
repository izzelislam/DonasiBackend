
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
      title="Donatur"
      page="Tambah"
      link="donors.index"
    />
  </x-slot>

  <x-card title="Buat data donatur">
    <form x-data="Donor" class="col-8" method="POST" action="{{ $action }}">
      @csrf
      @isset($model)
        @method('PUT')
      @endisset
      <x-form-select
        label="Team"
        name="team_id"
        :options="$teams"
        value="{{ $model->team_id ?? null }}"
      />

      <x-form-input
        label="Nama"
        name="name"
        placeholder="isikan nama donatur"
        value="{{ $model->name ?? '' }}"
      />
      <x-form-input
        label="No Hp"
        name="phone_number"
        placeholder="isikan no hp donatur"
        value="{{ $model->phone_number ?? '' }}"
      />

      {{-- provinsi --}}
      <div class="form-group">
        <label for="exampleFormControlSelect1">Provinsi</label>
        <select 
          class="form-control @error('province_id') is-invalid @enderror select2" 
          x-model="province_id" 
          id="exampleFormControlSelect1" 
          name="province_id" 
          x-on:change="getRegencies(province_id)"
        >
          @if (isset($model->province))
            <option value="{{ $model->province->id }}" selected>{{ $model->province->name }}</option>
          @else
            <option value=""></option>
          @endif
          @foreach ($provinces as $province)
            <option value="{{ $province->id }}">{{ $province->name }}</option>
          @endforeach
        </select>
      
        <small class="form-text text-muted">Pilih salah satu.</small>
        @error('province_id')
          <span class="invalid-feedback">
            {{ $message }}
          </span>
        @enderror
      </div>

      {{-- kabupaten --}}
      <div class="form-group">
        <label for="exampleFormControlSelect1">Kabupaten</label>
        <select 
          class="form-control @error('regency_id') is-invalid @enderror" 
          x-model="regency_id" 
          id="exampleFormControlSelect1" 
          name="regency_id"" 
          x-on:change="getDistrict(regency_id)"
        >
        
        @if(isset($model->regency))
          <option value="{{ $model->regency->id }}" selected>{{ $model->regency->name }}</option>
        @else
          <option value=""></option>
        @endif

        <template x-for="regency in res_regencies">
          <option :value="regency.id"><span x-text="regency.name"></span></option>
        </template>
          
        </select>
      
        <small class="form-text text-muted">Pilih salah satu.</small>
        @error('regency_id')
          <span class="invalid-feedback">
            {{ $message }}
          </span>
        @enderror
      </div>
       
      {{-- kecamatan --}}
      <div class="form-group">
        <label for="exampleFormControlSelect1">Kecamatan</label>
        <select 
          class="form-control @error('district_id') is-invalid @enderror" 
          id="exampleFormControlSelect1" 
          name="district_id" 
        >
        
        @if(isset($model->district))
          <option value="{{ $model->district->id }}" selected>{{ $model->district->name }}</option>
        @else
          <option value=""></option>
        @endif

        <template x-for="district in res_districts">
          <option :value="district.id"><span x-text="district.name"></span></option>
        </template>
        {{-- <option value="{{ $province->id }}">{{ $province->name }}</option> --}}
          
        </select>
      
        <small class="form-text text-muted">Pilih salah satu.</small>
        @error('district_id')
          <span class="invalid-feedback">
            {{ $message }}
          </span>
        @enderror
      </div>


      <x-form-textarea
        label="Alamat"
        name="address"
        placeholder="isikan alamat donatur"
        value="{{ $model->address ?? '' }}"
      />

      <div class="mb-4">
        <label for="">Pilih koordinat lokasi</label>
        <div id="mapid">
        </div>
        <button type="button" onclick="removeMarker()" class="btn btn-danger mt-2">
          Hapus marker
        </button>
      </div>

      <x-form-input
        label="Latitude (optional)"
        name="lat"
        placeholder="isikan lat (koordinat)"
        value="{{ $model->lat ?? '' }}"
        class="lat"
      />

      <x-form-input
        label="Longitude (optional)"
        name="lng"
        placeholder="isikan long (koordinat)"
        value="{{ $model->lng ?? '' }}"
        class="lng"
      />

      <button class="btn btn-primary"> {{ isset($model) ? 'Update' : 'Tambah' }} Data</button>
  </form>
  
  </x-card>

  <x-slot:addonscript>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>
    
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
     
    <script>
      const map = L.map('mapid').setView([-7.710991655433217, 110.43434095752946], 8);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      var markered = '';

      // window on load
      window.onload = function() {
        var lat = document.getElementsByClassName('lat')[0].value;
        var lng = document.getElementsByClassName('lng')[0].value;
        const markerConf = { draggable: true, autoPan: true }
        if(lat && lng) {
          var marker = L.marker([lat, lng], markerConf).addTo(map);
          markered = marker;
          console.log(markered);
          marker.on('dragend', function(e) {
            var marker = e.target;
            var position = marker.getLatLng();
            document.getElementsByClassName('lat')[0].value = position.lat;
            document.getElementsByClassName('lng')[0].value = position.lng;
          }); 
        }
      }
      
      map.on('click', function(e){
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        if (markered == '') {
          const markerConf = { draggable: true, autoPan: true }

          const marker = L.marker([lat, lng], markerConf).addTo(map);

          markered = marker;

          marker.on('dragend', function(e) {
            var marker = e.target;
            var position = marker.getLatLng();
            document.getElementsByClassName('lat')[0].value = position.lat;
            document.getElementsByClassName('lng')[0].value = position.lng;
          }); 

          // get element by class name
          var latInput = document.getElementsByClassName('lat')[0];
          var lngInput = document.getElementsByClassName('lng')[0];
          // get element by id lat
          latInput.value = lat;
          lngInput.value = lng;
        }
      });

      // remove marker
      function removeMarker(){
        map.removeLayer(markered);
        markered = '';
        document.getElementsByClassName('lat')[0].value = '';
        document.getElementsByClassName('lng')[0].value = '';
      }
    </script>

    <script>
      function Donor(){

        const regencies = @json($regencies);
        const districts = @json($districts);

        return {
          // data
          province_id: '',
          regency_id: '',
          res_regencies: [],
          res_districts: [],

          //method
          getRegencies(param){
            const newRegencies = regencies.filter(regency => regency.province_id == param);
            this.res_regencies = newRegencies;
          },

          getDistrict(param){
            const newDistricts = districts.filter(district => district.regency_id == param);
            this.res_districts = newDistricts;
          }
        }
      }
    </script>
  </x-slot>

</x-layouts.app>