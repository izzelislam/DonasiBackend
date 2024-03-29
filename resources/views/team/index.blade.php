<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="teams.index"
      title="TIM"
    />
  </x-slot>

  <x-card 
    title="Data Team" 
    :buttons="[
      'create'      => ['name' => 'create', 'url' => 'teams.create'],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>UUID</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($teams as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->uuid }}</td>
            <td>
              <a href="{{ route('teams.edit', $item->id) }}" class="btn btn-icon btn-3 btn-warning" >
                <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                <span class="btn-inner--text">edit</span>
              </a>
              <form action="{{ route('teams.destroy', $item->id) }}" method="POST" class="d-inline">
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