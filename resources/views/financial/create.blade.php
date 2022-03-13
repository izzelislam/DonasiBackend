@php
    function Label($param){
      if($param == 'income'){
        return 'masuk';
      }else{
        return 'keluar';
      }
    }
@endphp
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Keuangan"
      page="Tambah"
      link="financials.index"
    />
  </x-slot>

  <x-card title="Tambah data keuangan">
    <form class="col-8" method="POST" action="{{ $action }}">
      @csrf
      @isset($transaction)
        @method('PUT')
      @endisset
      <x-form-input
        label="Title"
        name="title"
        placeholder="isikan nama transaksi"
        value="{{ $transaction->title ?? '' }}"
      />

      <x-form-select
        label="Jenis Transaksi"
        name="type"
        :default="[ 'value' => $transaction->type ?? '', 'label' =>  isset($transaction) ? Label($transaction->type) : '' ]"
        :options="['income' =>'masuk', 'expense' => 'keluar']"
      />

      <x-form-input
        label="Nominal"
        name="amount"
        placeholder="isikan jumlah nominal"
        value="{{ $transaction->amount ?? '' }}"
      />
      
      <x-form-textarea
        label="Catatan"
        name="note"
        placeholder="isikan catatan"
        value="{{ $transaction->note ?? '' }}"
      />

      <button class="btn btn-primary">@isset($transaction) Update @else Tambah @endisset Data</button>
  </form>
  
  </x-card>
</x-layouts.app>