<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="contents.index"
      title="Content"
    />
  </x-slot>

  <x-card 
    title="Content" 
    :buttons="[
      'create'      => ['name' => 'create', 'url' => 'contents.create'],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Judul</th>
          <th>Gambar</th>
          <th>Tanggal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($contents as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->title }}</td>
            <td>
              <img src="{{ Storage::url($item->image) }}" alt="" style="width: 100px; height:100px;">
            </td>
            <td>{{ $item->created_at->format('d/m/y H:i:s') }}</td>
            <td>
              <a href="{{ route('contents.show', $item->id) }}" class="btn btn-icon btn-3 btn-info" >
                <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
                <span class="btn-inner--text">detail</span>
              </a>
              <a href="{{ route('contents.edit', $item->id) }}" class="btn btn-icon btn-3 btn-warning" >
                <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                <span class="btn-inner--text">edit</span>
              </a>
              <form action="{{ route('contents.destroy', $item->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
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