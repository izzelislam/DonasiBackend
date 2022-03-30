
<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb 
      title="Donatur" 
      page="index" 
      link="donors.index" 
    />
  </x-slot>

  {{-- filter --}}
  <div class="card mb-4 px-4 py-2">
    <form id="form" x-data="Filter()" action="{{ route('donors.index') }}" method="GET">
      @csrf
      <div class="row">
        
        <div class="col-3">
          <div class="form-group">
            <label for="">Pilih Provinsi</label>
            <select class="form-control" name="province_id" id="province_form" x-on:change="submit()">
              <option value="">-- pilih --</option>
              @foreach ($provinces as $province)
                  <option value="{{ $province->id }}" {{ $province->id == request()->province_id ? 'selected' : '' }}>{{ $province->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        
        <div class="col-3">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Kabupaten</label>
            <select class="form-control" id="regency_form" name="regency_id" x-on:change="submit()">
              <template x-if="regency">
                <option :value="regency.id" selected><span x-html="regency.name"></span></option>
              </template>
              <option value="">-- pilih --</option>
              <template x-for="regency in regencies">
                <option :value="regency.id"><span x-html="regency.name"></span></option>
              </template>
            </select>
          </div>
        </div>

        <div class="col-3">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Kecamatan</label>
            <select class="form-control" id="exampleFormControlSelect1" name="district_id" x-on:change="submit()">
              <template x-if="district">
                <option :value="district.id" selected><span x-html="district.name"></span></option>
              </template>
              <option value="">-- pilih --</option>
              <template x-for="district in districts">
                <option :value="district.id"><span x-html="district.name"></span></option>
              </template>
            </select>
          </div>
        </div>

        <div class="col-2">
          <div class="form-group">
            <label for="">Pilih Tim</label>
            <select class="form-control" name="team_id" id="team_id" onchange="this.form.submit()">
              <option value="">-- pilih --</option>
              @foreach ($teams as $team)
                  <option value="{{ $team->id }}" {{ $team->id == request()->team_id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
  
        <div class="col-1 pt-4">
          <div class="flex">
            <a href="{{ route('donors.index') }}" class="btn btn-primary">Reset</a>
          </div>
        </div>
      </div>
    </form>
  </div>


  <x-card 
    title="Data donasi" 
    :buttons="[
      'create'      => ['name' => 'create', 'url' => 'donors.create'],
      'export-data' => ['name' => 'export-data', 'url' => '/donors/export/excel?'.request()->getQueryString()],
    ]" 
  >
    
  <div class="table-responsive p-0">
      <table id="myTable" class="table" style="width:100%">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>UID</th>
            <th>No. Telp</th>
            <th>Kabupaten</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($donors as $index => $item)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->uuid }}</td>
              <td>{{ $item->phone_number }}</td>
              <td>{{ $item->regency->name ?? '' }}</td>
              <td>
                <span class="badge badge-pill bg-{{ $item->status == 'active' ? 'success' : 'danger' }}">{{ $item->status == 'active' ? 'Aktif' : 'TIdak-Aktif' }}</span>
              </td>
              <td>
                <a href="{{ route('donors.show', $item->id) }}" class="btn btn-icon btn-3 btn-info" >
                  <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
                  <span class="btn-inner--text">Detail</span>
                </a>
                <form action="{{ route('donors.status', $item->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-icon btn-3 btn-primary" >
                    <span class="btn-inner--icon"><i class="fas fa-flask"></i></span>
                    <span class="btn-inner--text">Status</span>
                  </button>
                </form>
                <a href="{{ route('donors.edit', $item->id) }}" class="btn btn-icon btn-3 btn-warning" >
                  <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                  <span class="btn-inner--text">edit</span>
                </a>
                <form action="{{ route('donors.destroy', $item->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('delete')
                  <button class="btn btn-icon btn-3 btn-danger" >
                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                    <span class="btn-inner--text">delete</span>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </x-card>
  
  <x-slot:addonscript>
    <x-table-script/>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>
    <script>
      function Filter(){
        regenciesData = @json($regencies);
        districtData = @json($districts);
        return{
          // data
          form : document.getElementById('form'),
          regencies: [],
          districts:[],
          regency_id: '',
          regency: null,
          district: null,
          // method
          submit(param){
            // get element province form
            this.form.submit();
          },

          getRegency(id){
            // get regency by id
            let regency = this.regencies.find(regency => regency.id == id);
            return regency;
          },
          init(){
            let province_id = document.getElementById('province_form');
            let regency_id = document.getElementById('regency_form');

            //  set regency
            this.regencies = regenciesData.filter(item => item.province_id == province_id.value);
            
            // get query string
            let query = new URLSearchParams(window.location.search);
            // get regency id
            let regency_id_query = query.get('regency_id');
            // get district id
            let district_id_query = query.get('district_id');
           
            if (regency_id_query != null && regency_id_query != '') {
      
              r= regenciesData.find(item => item.id == regency_id_query);

              console.log(regency_id_query)

              // set district
              this.districts = districtData.filter(item => item.regency_id == r.id);
              this.regency=r;
            }
            
            if (district_id_query != null && district_id_query != '') {
              
              d= districtData.find(item => item.id == district_id_query);
              this.district=d;
            }
          }
        }
      }
    </script>
  </x-slot>
</x-layouts.app>
