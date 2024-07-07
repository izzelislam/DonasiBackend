<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="permits.index"
      title="Perizinan"
    />
  </x-slot>

  <x-card 
    title="Data Perizinan" 
  >

  <div class="p-0">
    <livewire:permit-table/>
  </div>
    
  </x-card>
  
</x-layouts.app>