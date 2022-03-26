<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="fundrisers.index"
      title="Fundriser"
    />
  </x-slot>

  <x-card 
    title="Data Fundriser" 
    :buttons="[
      'create'      => ['name' => 'create', 'url' => 'fundrisers.create'],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Photo</th>
          <th>email</th>
          <th>No. Telp</th>
          <th>Tim</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($fundrisers as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->name }}</td>
            <td>
              <img src="{{ asset($item->image) }}" alt="" style="width: 100px; height:100px;">
            </td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->phone_number }}</td>
            <td>{{ $item->team->name }}</td>
            <td>
              <span class="badge badge-pill bg-{{ $item->status == 'active' ? 'success' : 'danger' }}">{{ $item->status == 'active' ? 'Aktif' : 'TIdak-Aktif' }}</span>
            </td>
            <td>
              <a href="{{ route('fundrisers.edit', $item->id) }}" class="btn btn-icon btn-3 btn-warning" >
                <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                <span class="btn-inner--text">edit</span>
              </a>
              <form action="{{ route('fundrisers.status', $item->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-icon btn-3 btn-primary" >
                  <span class="btn-inner--icon"><i class="fas fa-flask"></i></span>
                  <span class="btn-inner--text">Status</span>
                </button>
              </form>
              <form action="{{ route('fundrisers.destroy', $item->id) }}" method="POST" class="d-inline">
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
  </x-slot>

</x-layouts.app>