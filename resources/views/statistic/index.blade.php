<x-layouts.app>

  <script src="{{ asset('argon/assets/js/plugins/chartjs.min.js') }}"></script> 
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="statistic.index"
      title="Statistik Donasi"
    />
  </x-slot>

  <div class="card mb-4 px-4 py-2">
    <form action="{{ route('statistic.index') }}" method="GET">
      @csrf
      <div class="row">
        <div class="col-4">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Tahun</label>
            <select class="form-control" id="exampleFormControlSelect1" name="year" onchange="this.form.submit()">
              <option>-- pilih tahun --</option>
              @foreach ($years as $year)
                <option value="{{ $year->year}}" {{ request()->year == $year->year ? 'selected' : '' }} >{{ $year->year }}</option>
              @endforeach
            </select>
          </div>
        </div>
  
        <div class="col-2 pt-4">
          <div class="flex">
            <a href="{{ route('statistic.index') }}" class="btn btn-primary">Reset</a>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="card mb-4">
    <h4 class="mt-0 p-3 text-center">STATISTIK KEUANGAN DAN DONASI TAHUN {{ isset(request()->year) ? request()->year : date('Y') }}</h4>
  </div>

  @include('statistic.donation_performance')
  @include('statistic.card_item')
  @include('statistic.recap')

</x-layouts.app>