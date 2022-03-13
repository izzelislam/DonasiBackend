<div class="card mb-4 px-4 py-2">
  <form method="GET" action="{{ route('reports.donations') }}">
    <div class="row">
      @csrf
      <div class="col-2">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Pilih Tim</label>
          <select class="form-control" id="exampleFormControlSelect1" name="team_id" onchange="this.form.submit()">
            <option value="">-- pilih tim --</option>
            @foreach ($teams as $team)
              <option value="{{ $team->id }}" {{ request()->team_id == $team->id ? 'selected' : '' }} >{{ $team->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Pilih Bulan</label>
          <select class="form-control" id="exampleFormControlSelect1" name="month" onchange="this.form.submit()">
            <option value="">-- pilih bulan --</option>
            @foreach ($month as $key => $value)
              <option value="{{ $key }}" {{ request()->month == $key ? 'selected' : '' }} >{{ $value }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Pilih Tahun</label>
          <select class="form-control" id="exampleFormControlSelect1" name="year" onchange="this.form.submit()">
            <option value="">-- pilih tahun --</option>  
            @foreach ($years as $year )
              <option value="{{ $year->year }}" {{ request()->year == $year->year ? 'selected' : '' }} >{{ $year ->year}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <label for="example-date-input" class="form-control-label">Pilih Tanggal dari</label>
          <input name="start_date" class="form-control" type="date" value="{{ request()->start_date ?? '' }}"  onchange="this.form.submit()" id="example-date-input">
        </div>
      </div>

      <div class="col-2">
        <div class="form-group">
          <label for="example-date-input" class="form-control-label">Pilih Tanggal Hingga</label>
          <input name="end_date" class="form-control" type="date" value="{{ request()->end_date ?? '' }}"  onchange="this.form.submit()" id="example-date-input">
        </div>
      </div>

      <div class="col-2 pt-4">
        <div class="flex">
          <a href="{{ route('reports.donations') }}" class="btn btn-primary">Reset</a>
        </div>
      </div>
    </div>
  </form>
</div>
