@isset($approve)
@php
  $path_approve = explode("/",parse_url($approve)["path"]);
  $id_approve   = end($path_approve);
@endphp

<button type="button" class="btn btn-success btn-circle btn-sm"  data-bs-toggle="modal" data-bs-target="#confirmApprove{{ $id_approve }}Center">
  <i class="fas fa-check me-1"></i>
</button>

<div class="modal fade" id="confirmApprove{{ $id_approve }}Center" tabindex="-1" aria-labelledby="confirmApprove" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="w-100 text-center">
          <h5 class="modal-title" id="confirmApprove{{ $id_approve }}CenterTitle"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Menerima Permintaan</h5>
        </div>
      </div>
      <div class="modal-body">

        <form  class="d-inline" action="{{ $approve }}" method="POST">
          @csrf
          {{-- <div class="text-center">
            <img width="80%" src="/assets/ilustration/soft-delete-confirmation.png" alt="">
          </div> --}}
          <div class="text-center">
            Apakah anda yakin ingin menerima permintaan ini? 
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times  me-2"></i>Batal</button>
          <button class="btn btn-success"> <i class="fas fa-check me-2"></i> Terima</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endisset

@isset($reject)
@php
  $path = explode("/",parse_url($reject)["path"]);
  $id_approve_reject   = end($path);
@endphp

<button type="button" class="btn btn-danger btn-circle btn-sm"  data-bs-toggle="modal" data-bs-target="#confirmReject{{ $id_approve_reject }}Center">
  <i class="fas fa-times me-1"></i>
</button>

<div class="modal fade" id="confirmReject{{ $id_approve_reject }}Center" tabindex="-1" aria-labelledby="confirmReject{{ $id_approve_reject }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="w-100 text-center">
          <h5 class="modal-title" id="confirmReject{{ $id_approve_reject }}CenterTitle"><i class="fas fa-exclamation-triangle"></i> Konfirmasi tolak permintaan</h5>
        </div>
      </div>
      <div class="modal-body">

        <form  class="d-inline" action="{{ $reject }}" method="POST">
          @csrf
          {{-- <div class="text-center">
            <img width="80%" src="/assets/ilustration/delete-confirmation.png" alt="">
          </div> --}}
          <div class="text-center">
            Apakah anda yakin menolak permintaan ini?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times  me-2"></i>Batal</button>
          <button class="btn btn-danger"> <i class="fas fa-times me-2"></i> Tolak</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endisset

@isset($restore)
  @include("components.livewire.restore-button")
@endisset

@isset($history)
<form class="d-inline" action="{{ $history }}" method="POST">
  @csrf @method('PUT')
  <button class="btn btn-success btn-circle switch-status btn-sm">
    <i class="fas fa-redo"></i>
  </button>
</form>
@endisset

@isset($detail)
<a href="{{ $detail }}" class="btn btn-icon btn-3 btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
  <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
  <span class="btn-inner--text">Detail</span>
</a>
@endisset

@isset($edit)
<a href="{{ $edit }}" class="btn btn-warning btn-circle btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
  <i class="fas fa-edit"></i>
</a>
@endisset


@isset($delete)
  @php
    $path_delete = explode("/",parse_url($delete)["path"]);
    $id_delete   = end($path_delete);
  @endphp

  <button type="button" class="btn btn-icon btn-3 btn-danger"  data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $id_delete }}Center">
    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
    <span class="btn-inner--text">delete</span>
  </button>

  <div class="modal fade" id="confirmDelete{{ $id_delete }}Center" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 text-center">
            <h5 class="modal-title" id="confirmDelete{{ $id_delete }}CenterTitle"><i class="fas fa-exclamation-triangle"></i> Konfirmasi hapus data</h5>
          </div>
        </div>
        <div class="modal-body">

          <form  class="d-inline" action="{{ $delete }}" method="POST">
            @csrf @method('DELETE')
            {{-- <div class="text-center">
              <img width="80%" src="/assets/ilustration/soft-delete-confirmation.png" alt="">
            </div> --}}
            <div class="text-center">
             Data yang dihapus tidak dapat dikembalikan.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times  me-2"></i>Batal</button>
            <button class="btn btn-danger"> <i class="fas fa-trash me-2"></i> Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endisset
