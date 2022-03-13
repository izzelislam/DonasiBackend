@props(['title' => '', 'buttons'])

<div>
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>{{ $title }}</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="px-4 my-3">
            @if (isset($buttons))
            <div class="row">
              <div class="col">
                <span class="float-end ">
                  @if (isset($buttons['export-data']))
                    <a href="{{ $buttons['export-data']['url'] }}" class="btn btn-icon btn-3 btn-primary" type="button">
                      <span class="btn-inner--icon"><i class="far fa-file-excel"></i></span>
                      <span class="btn-inner--text">export data</span>
                    </a>
                  @endif
                  @if (isset($buttons['export-qr']))
                    <a href="{{ route($buttons['export-qr']['url']) }}" class="btn btn-icon btn-3 btn-info" type="button">
                      <span class="btn-inner--icon"><i class="fas fa-qrcode"></i></span>
                      <span class="btn-inner--text">export Qr</span>
                    </a>
                  @endif
                  @if (isset($buttons['print']))
                    <a href="{{ $buttons['print']['url'] }}" class="btn btn-icon btn-3 btn-primary" type="button">
                      <span class="btn-inner--icon"><i class="fas fa-print"></i></span>
                      <span class="btn-inner--text">Cetak pdf</span>
                    </a>
                  @endif
                  @if (isset($buttons['create']['name']))
                    <a href="{{ route($buttons['create']['url']) }}" class="btn btn-icon btn-3 btn-success" type="button">
                      <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                      <span class="btn-inner--text">tambah data</span>
                    </a>
                  @endif
                </span>
              </div>
            </div>
            @endif
            {{ $slot }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>