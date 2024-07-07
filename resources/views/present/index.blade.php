<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="presents.index"
      title="Presensi"
    />
  </x-slot>

  <x-card 
    title="Data Presensi" 
  >

  <div class=" p-0">
    <livewire:present-table/>
  </div>
    
  </x-card>
  
  <x-slot:addonscript>
    <x-table-script/>
  </x-slot>

</x-layouts.app>